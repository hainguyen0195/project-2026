export type Product = {
  id: number;
  name: string;
  code: string;
  category: string;
  price: number;
  status: "published" | "draft";
  color: string;
};

export const initialProducts: Product[] = [
  { id: 1, name: "Ghế thư giãn Nordic", code: "CM-GH-001", category: "Phòng khách", price: 2450000, status: "published", color: "sand" },
  { id: 2, name: "Đèn bàn Arco", code: "CM-DE-012", category: "Trang trí", price: 890000, status: "published", color: "sage" },
  { id: 3, name: "Bàn trà tối giản", code: "CM-BA-008", category: "Phòng khách", price: 3200000, status: "draft", color: "rose" },
  { id: 4, name: "Bình gốm Haze", code: "CM-TT-023", category: "Trang trí", price: 450000, status: "published", color: "clay" },
  { id: 5, name: "Kệ gỗ lắp ghép", code: "CM-KE-006", category: "Phòng làm việc", price: 1850000, status: "published", color: "blue" },
];

export const demoOrders = [
  { code: "#CM1028", customer: "Nguyễn Minh Anh", initials: "MA", items: 2, total: 3340000, status: "Chờ xác nhận", time: "10:42", color: "sand" },
  { code: "#CM1027", customer: "Trần Hoàng Nam", initials: "HN", items: 1, total: 2450000, status: "Đang giao", time: "10:18", color: "sage" },
  { code: "#CM1026", customer: "Lê Thu Hà", initials: "TH", items: 3, total: 4500000, status: "Hoàn tất", time: "09:56", color: "rose" },
  { code: "#CM1025", customer: "Phạm Đức Huy", initials: "DH", items: 1, total: 890000, status: "Hoàn tất", time: "09:30", color: "blue" },
];

export const articles = [
  { title: "Một không gian nhỏ, nhiều cảm hứng lớn", category: "Không gian sống", date: "15/09/2026", status: "Đã xuất bản", color: "sage" },
  { title: "Chọn chất liệu tự nhiên cho ngôi nhà của bạn", category: "Cẩm nang", date: "14/09/2026", status: "Bản nháp", color: "sand" },
  { title: "Bộ sưu tập mới: Những khoảng lặng", category: "Tin tức", date: "12/09/2026", status: "Đã xuất bản", color: "rose" },
];

export const money = (value: number) => new Intl.NumberFormat("vi-VN", { style: "currency", currency: "VND" }).format(value);
