import { CmsLocaleProvider } from "../admin/cms-locale";
import CmsTheme from "../admin/cms-theme";
import "../globals.css";

export const metadata = { title: "Đăng nhập · Quản trị", robots: { index: false, follow: false } };

export default function LoginLayout({ children }: { children: React.ReactNode }) {
  return <><CmsTheme /><CmsLocaleProvider>{children}</CmsLocaleProvider></>;
}
