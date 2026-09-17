<?php
class AdminSession
{
    public static function establish($d, $row)
    {
        global $loginAdmin;
        session_regenerate_id(true);
        $token = bin2hex(random_bytes(32));
        $sessionhash = md5(sha1($row['password'] . $row['username']));
        $timenow = time();
        $d->rawQuery("insert into #_user_log (id_user, ip, timelog, user_agent) values (?,?,?,?)", [$row['id'], $_SERVER['REMOTE_ADDR'] ?? '', $timenow, $_SERVER['HTTP_USER_AGENT'] ?? '']);
        $d->rawQuery("update #_user set login_session = ?, lastlogin = ?, user_token = ?, secret_key = ? where id = ?", [$sessionhash, $timenow, $token, $sessionhash, $row['id']]);
        $d->rawQuery("update #_user_limit set login_attempts = 0, locked_time = 0 where login_ip = ?", [$_SERVER['REMOTE_ADDR'] ?? '']);
        $_SESSION[$loginAdmin] = array_intersect_key($row, array_flip(['id', 'username', 'fullname', 'phone', 'email', 'role', 'password']));
        $_SESSION[$loginAdmin] += ['active' => true, 'secret_key' => $sessionhash, 'token' => $sessionhash, 'login_session' => $sessionhash, 'login_token' => $token];
        $_SESSION['isLoggedIn'] = $token;
        $_SESSION[TOKEN] = true;
        unset($_SESSION['passkey_challenge'], $_SESSION['passkey_csrf']);
    }
}
