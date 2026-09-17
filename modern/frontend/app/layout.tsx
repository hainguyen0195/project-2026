import type { Metadata } from "next";
import "./base.css";

export const metadata: Metadata = {
  title: "Website",
};

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="vi" suppressHydrationWarning>
      <body>{children}</body>
    </html>
  );
}
