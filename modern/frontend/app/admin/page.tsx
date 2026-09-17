import AdminStudio from "./studio";
import CmsAuth from "./cms-auth";

export default function AdminPage() {
  return <CmsAuth><AdminStudio /></CmsAuth>;
}
