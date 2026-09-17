<?php
class AdminPasskey
{
    private $pdo;
    private $prefix;
    private $settings;
    private $webauthn;

    public function __construct($database, $settings)
    {
        if (empty($settings['enabled']) || !is_file(__DIR__ . '/../passkey/vendor/autoload.php')) {
            throw new RuntimeException('Passkey chưa được bật hoặc chưa cài thư viện.');
        }
        $origin = parse_url($settings['origin']);
        if (!$origin || ($origin['scheme'] ?? '') !== 'https' || ($origin['host'] ?? '') !== $settings['rp_id'] || isset($origin['path']) || isset($origin['query']) || isset($origin['fragment']) || isset($origin['user'])) {
            throw new RuntimeException('Cấu hình HTTPS/origin của Passkey không hợp lệ.');
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $database['prefix'])) {
            throw new RuntimeException('Database prefix không hợp lệ.');
        }
        require_once __DIR__ . '/../passkey/vendor/autoload.php';
        $this->settings = $settings;
        $this->prefix = $database['prefix'];
        $this->pdo = new PDO('mysql:host=' . $database['host'] . ';port=' . $database['port'] . ';dbname=' . $database['dbname'] . ';charset=utf8mb4', $database['username'], $database['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]);
        $this->webauthn = new \lbuchs\WebAuthn\WebAuthn($settings['rp_name'], $settings['rp_id'], ['none'], true);
    }

    private function query($sql, $params = [])
    {
        $statement = $this->pdo->prepare(str_replace('#_', $this->prefix, $sql));
        $statement->execute($params);
        return $statement;
    }

    public static function encode($bytes)
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    public static function decode($value)
    {
        if (!is_string($value) || $value === '' || strlen($value) > 100000 || !preg_match('/^[A-Za-z0-9_-]+$/D', $value)) {
            throw new RuntimeException('Dữ liệu Passkey không hợp lệ.');
        }
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);
        if ($decoded === false || self::encode($decoded) !== $value) {
            throw new RuntimeException('Dữ liệu Passkey không hợp lệ.');
        }
        return $decoded;
    }

    public function throttle($key, $limit = 30)
    {
        $bucket = hash('sha256', $key);
        $now = time();
        $this->query('INSERT INTO #_user_passkey_limit (bucket, attempts, expires_at) VALUES (?, 1, ?) ON DUPLICATE KEY UPDATE attempts = IF(expires_at < ?, 1, attempts + 1), expires_at = IF(expires_at < ?, VALUES(expires_at), expires_at)', [$bucket, $now + 900, $now, $now]);
        $attempts = $this->query('SELECT attempts FROM #_user_passkey_limit WHERE bucket = ?', [$bucket])->fetchColumn();
        if ($attempts > $limit) {
            throw new RuntimeException('Quá nhiều yêu cầu. Vui lòng thử lại sau 15 phút.');
        }
    }

    public function currentUser()
    {
        global $loginAdmin;
        $session = $_SESSION[$loginAdmin] ?? [];
        $user = $this->query("SELECT * FROM #_user WHERE id = ? AND FIND_IN_SET('hienthi', status)", [$session['id'] ?? 0])->fetch(PDO::FETCH_ASSOC);
        if (!$user || empty($session['active']) || empty($_SESSION[TOKEN]) || !hash_equals(md5(sha1($user['password'] . $user['username'])), $session['login_session'] ?? '') || time() - $user['lastlogin'] > 3600) {
            throw new RuntimeException('Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại.');
        }
        return $user;
    }

    public function verifyPassword($user, $password)
    {
        global $config;
        $this->throttle('password:' . $user['id'], 5);
        if (!is_string($password) || !hash_equals($user['password'], md5($config['website']['secret'] . $password . $config['website']['salt']))) {
            throw new RuntimeException('Mật khẩu hiện tại không chính xác.');
        }
    }

    public function credentials($user)
    {
        return $this->query('SELECT id, name, created_at, last_used_at FROM #_user_passkey WHERE id_user = ? ORDER BY id DESC', [$user['id']])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function remove($user, $id)
    {
        $this->query('DELETE FROM #_user_passkey WHERE id = ? AND id_user = ?', [$id, $user['id']]);
    }

    public function options($kind, $user = null, $name = '')
    {
        if ($kind === 'register') {
            $credentials = $this->query('SELECT credential_id FROM #_user_passkey WHERE id_user = ?', [$user['id']])->fetchAll(PDO::FETCH_COLUMN);
            if (count($credentials) >= 10) {
                throw new RuntimeException('Tối đa 10 Passkey mỗi tài khoản.');
            }
            $handle = hash('sha256', $this->settings['rp_id'] . ':' . $user['id'], true);
            $options = $this->webauthn->getCreateArgs($handle, $user['username'], $user['fullname'] ?: $user['username'], 120, 'required', true, null, array_map([self::class, 'decode'], $credentials));
        } else {
            $options = $this->webauthn->getGetArgs([], 120, true, true, true, true, true, true);
        }
        $_SESSION['passkey_challenge'] = ['kind' => $kind, 'challenge' => $this->webauthn->getChallenge()->getBinaryString(), 'expires' => time() + 120, 'user_id' => $user['id'] ?? null, 'password' => $user['password'] ?? null, 'name' => $name];
        return $options;
    }

    public function finish($kind, $input)
    {
        $pending = $_SESSION['passkey_challenge'] ?? [];
        unset($_SESSION['passkey_challenge']);
        if (($pending['kind'] ?? '') !== $kind || ($pending['expires'] ?? 0) < time()) {
            throw new RuntimeException('Yêu cầu đã hết hạn hoặc đã sử dụng. Vui lòng thử lại.');
        }
        $clientData = self::decode($input['clientDataJSON'] ?? null);
        $client = json_decode($clientData, true);
        if (!is_array($client) || ($client['origin'] ?? '') !== $this->settings['origin'] || ($client['crossOrigin'] ?? false) !== false) {
            throw new RuntimeException('Nguồn yêu cầu Passkey không hợp lệ.');
        }
        $rawId = self::decode($input['id'] ?? null);
        if (strlen($rawId) > 1024) {
            throw new RuntimeException('Passkey không hợp lệ.');
        }
        if ($kind === 'register') {
            $user = $this->currentUser();
            if ((string)$user['id'] !== (string)$pending['user_id'] || !hash_equals($user['password'], $pending['password'])) {
                throw new RuntimeException('Tài khoản đã thay đổi. Vui lòng thử lại.');
            }
            $data = $this->webauthn->processCreate($clientData, self::decode($input['attestationObject'] ?? null), $pending['challenge'], true, true);
            if (!hash_equals($rawId, $data->credentialId)) {
                throw new RuntimeException('Passkey không hợp lệ.');
            }
            $this->query('INSERT INTO #_user_passkey (id_user, credential_id, credential_hash, public_key, sign_count, name, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)', [$user['id'], self::encode($rawId), hash('sha256', $rawId), $data->credentialPublicKey, $data->signatureCounter ?? 0, $pending['name'], time()]);
            return null;
        }
        $this->pdo->beginTransaction();
        try {
            $credential = $this->query('SELECT * FROM #_user_passkey WHERE credential_hash = ? FOR UPDATE', [hash('sha256', $rawId)])->fetch(PDO::FETCH_ASSOC);
            if (!$credential || !hash_equals($credential['credential_id'], self::encode($rawId))) {
                throw new RuntimeException('Không thể xác thực Passkey.');
            }
            $user = $this->query("SELECT * FROM #_user WHERE id = ? AND FIND_IN_SET('hienthi', status)", [$credential['id_user']])->fetch(PDO::FETCH_ASSOC);
            if (!$user || !hash_equals(hash('sha256', $this->settings['rp_id'] . ':' . $user['id'], true), self::decode($input['userHandle'] ?? null))) {
                throw new RuntimeException('Không thể xác thực Passkey.');
            }
            $this->webauthn->processGet($clientData, self::decode($input['authenticatorData'] ?? null), self::decode($input['signature'] ?? null), $credential['public_key'], $pending['challenge'], (int)$credential['sign_count'], true, true);
            $this->query('UPDATE #_user_passkey SET sign_count = ?, last_used_at = ? WHERE id = ?', [$this->webauthn->getSignatureCounter() ?? 0, time(), $credential['id']]);
            $this->pdo->commit();
            return $user;
        } catch (Throwable $error) {
            $this->pdo->rollBack();
            throw $error;
        }
    }
}
