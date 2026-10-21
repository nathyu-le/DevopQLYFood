#!/usr/bin/env python3
"""Regenerate the evidence-based hosting diagram. Requires Pillow."""
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont

ROOT = Path(__file__).resolve().parents[1]
im = Image.new("RGB", (1800, 1220), "#f6f8f3")
d = ImageDraw.Draw(im)
FONT = "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf"
BOLD = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"

def text(x, y, value, size=24, color="#304e3e", bold=False):
    font = ImageFont.truetype(BOLD if bold else FONT, size)
    d.text((x, y), value, font=font, fill=color)

def box(x, y, w, h, title, lines, fill="#ffffff"):
    d.rounded_rectangle((x, y, x+w, y+h), radius=19, fill=fill,
                        outline="#d9e3d7", width=2)
    text(x+22, y+20, title, 25, "#244c3b", True)
    for i, line in enumerate(lines):
        text(x+22, y+62+i*31, line, 19, "#61755d")

def arrow(points, color="#75946f"):
    d.line(points, fill=color, width=4)
    x, y = points[-1]
    px, py = points[-2]
    if x > px:
        tip = [(x,y), (x-12,y-7), (x-12,y+7)]
    elif x < px:
        tip = [(x,y), (x+12,y-7), (x+12,y+7)]
    elif y > py:
        tip = [(x,y), (x-7,y-12), (x+7,y-12)]
    else:
        tip = [(x,y), (x-7,y+12), (x+7,y+12)]
    d.polygon(tip, fill=color)

text(75, 42, "DEVOPQLYFOOD  /  WEEK 1  /  21.10.2026", 21, "#8a9c7a", True)
text(75, 84, "Kiến trúc hosting hiện tại", 47, "#244c3b", True)
text(75, 154, "https://thitruongmmo.com/DevopQLYFood/public/", 27, "#5d7c58")
text(75, 202, "Nguồn: ảnh cPanel, ảnh app/SQL và kiểm tra HTTP; thông số chưa biết được ghi rõ.", 21, "#7a8d74")

d.rounded_rectangle((500, 260, 1720, 1020), radius=26,
                    fill="#edf3e9", outline="#c6d7bf", width=3)
text(535, 281, "HOSTING cp1 · Linux x86_64 · Shared IP 160.191.243.50", 24, "#3b6348", True)
text(535, 321, "Phạm vi dịch vụ hosting; chưa xác minh vị trí máy DB hoặc reverse proxy.", 19, "#61755d")

box(75, 390, 330, 155, "Máy khách", ["Trình duyệt web", "Laptop / điện thoại"])
arrow([(405, 460), (550, 460)])
text(418, 420, "HTTPS", 19, "#5d7c58", True)
text(418, 478, "443", 19, "#5d7c58", True)
box(550, 385, 500, 165, "Web server", ["cPanel báo Apache 2.4.67", "Domain root: /public_html", "Force HTTPS Redirect: Off"])
box(1140, 385, 530, 165, "Ứng dụng PHP", ["/DevopQLYFood/public/", "Session · CSRF · PDO CRUD", "PHP version: chưa xác minh"])
arrow([(1050, 465), (1140, 465)])

box(550, 625, 500, 175, "Source trong tài khoản hosting", ["URL suy ra: public_html/", "DevopQLYFood; chờ File Manager", ".htaccess chặn riêng: chờ upload"])
box(1140, 625, 530, 175, "MariaDB 10.11.16", ["Theo Server Information", "Bảng dishes · ảnh SELECT ID 9", "DB host/port: chưa xác minh"])
arrow([(1405, 550), (1405, 625)])
text(1420, 574, "PDO đọc / ghi", 19, "#5d7c58", True)
arrow([(800, 625), (800, 585), (1280, 585), (1280, 550)])

box(75, 720, 330, 175, "Quản trị hosting", ["cPanel / File Manager", "phpMyAdmin", "SSH: chưa xác minh"])
arrow([(405, 775), (550, 775)], "#a79a70")
box(1140, 865, 530, 110, "Dữ liệu phía hosting", ["Datadir / backup: chưa xác minh"])
arrow([(1405, 800), (1405, 865)])
text(550, 861, "Còn thiếu bằng chứng:", 22, "#8f773f", True)
text(550, 901, "Distro/version OS · CPU/RAM/disk quota", 19, "#8f773f")
text(550, 932, "PHP version · quyền SSH · firewall/ports", 19, "#8f773f")
text(550, 963, "Chưa kết luận đã đủ hạ tầng Week 1.", 19, "#8f773f")

d.line((75, 1060, 1720, 1060), fill="#dce5d7", width=2)
text(75, 1080, "Đã có: login/health HTTP 200; ảnh thêm/sửa món và SELECT cùng ID 9 trên DB hosting.", 23, "#52714f")
text(75, 1124, "Lưu ý: schema/README còn tải được trước bản sửa; cần upload .htaccess và xác nhận HTTP 403.", 21, "#8f773f")
text(75, 1162, "Ubuntu/VM/VPS là phương án tham chiếu trong docs/MANUAL_SETUP.md; chưa triển khai môi trường đó.", 19, "#7a8d74")
im.save(ROOT / "architecture.png", optimize=True)
print(ROOT / "architecture.png")
