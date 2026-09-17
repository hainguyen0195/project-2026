import { CmsLocaleProvider } from "./cms-locale";
import Notifications from "./notifications";
import CmsTheme from "./cms-theme";
import "../globals.css";

export const metadata = { title: "COMI Studio · Quản trị", robots: { index: false, follow: false } };

export default function AdminLayout({ children }: { children: React.ReactNode }) {
  return <><CmsTheme /><CmsLocaleProvider>{children}<Notifications /></CmsLocaleProvider></>;
}
