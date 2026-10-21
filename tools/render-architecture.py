#!/usr/bin/env python3
"""Regenerate architecture.png. Requires Pillow only for diagram generation."""
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont

ROOT = Path(__file__).resolve().parents[1]
W, H = 1800, 1220
im = Image.new('RGB', (W, H), '#f6f8f3')
d = ImageDraw.Draw(im)
FONT = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf'
BOLD = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf'
def f(size=24, bold=False):
    return ImageFont.truetype(BOLD if bold else FONT, size)
def text(x,y,t,size=24,color='#304e3e',bold=False):
    d.text((x,y),t,font=f(size,bold),fill=color)
def box(x,y,w,h,title,lines,fill='#ffffff',border='#d9e3d7',label=None):
    d.rounded_rectangle((x,y,x+w,y+h),radius=19,fill=fill,outline=border,width=2)
    offset=23
    if label:
        text(x+24,y+18,label,15,'#8b9e84',True)
        offset=46
    text(x+24,y+offset,title,25,'#244c3b',True)
    for i,line in enumerate(lines):
        text(x+24,y+offset+42+i*32,line,19,'#788b76')
def arrow(points,color='#75946f',width=4):
    d.line(points,fill=color,width=width)
    x,y=points[-1];px,py=points[-2]
    if x>px: tri=[(x,y),(x-12,y-7),(x-12,y+7)]
    elif x<px: tri=[(x,y),(x+12,y-7),(x+12,y+7)]
    elif y>py: tri=[(x,y),(x-7,y-12),(x+7,y-12)]
    else: tri=[(x,y),(x-7,y+12),(x+7,y+12)]
    d.polygon(tri,fill=color)

text(75,53,'DEVOPQLYFOOD  /  WEEK 1',21,'#8a9c7a',True)
text(75,94,'Kiến trúc triển khai dự kiến',47,'#244c3b',True)
text(75,164,'Website quản lý thực đơn quán ăn · PHP + MySQL',25,'#7a8d74')
d.rounded_rectangle((75,228,777,274),radius=12,fill='#f5ebd4')
text(93,238,'THIẾT KẾ ĐỀ XUẤT — CHƯA TRIỂN KHAI SERVER NHÓM',16,'#a1803e',True)

d.rounded_rectangle((830,230,1720,1010),radius=26,fill='#edf3e9',outline='#c6d7bf',width=3)
text(865,251,'SERVER · Ubuntu VM / VPS',24,'#3b6348',True)
text(865,290,'Loại môi trường, OS và tài nguyên thật: xác định khi triển khai',17,'#7e9477')

box(75,340,330,178,'Máy khách',['Trình duyệt web','Laptop / điện thoại'],label='CLIENT')
box(477,357,267,142,'Mạng truy cập',['Internet / mạng VM'],fill='#f1f3e7')
arrow([(405,426),(477,426)])
arrow([(744,426),(868,426)])
text(756,389,'HTTP 80',16,'#5d7c58',True)

box(868,337,814,108,'Firewall / security group',['HTTP cho client · SSH cho nguồn quản trị · Không public MySQL'])
arrow([(1040,445),(1040,487)])
box(868,487,345,134,'Apache',['Nhận request HTTP','DocumentRoot: public/'])
box(1300,487,382,134,'Ứng dụng PHP',['Session · CSRF · validation','CRUD món ăn bằng PDO'])
arrow([(1213,552),(1300,552)])
arrow([(1490,621),(1490,708)])
text(1218,646,'127.0.0.1 : 3306',18,'#5d7c58',True)

box(868,708,345,135,'Source & cấu hình',['Source trên ổ đĩa','config/ ở ngoài public/'])
box(1300,708,382,135,'MySQL',['Bảng dishes · InnoDB','DB user chỉ có quyền CRUD'])
box(868,906,814,73,'Ổ đĩa server · Dữ liệu MySQL',[],fill='#ffffff')
arrow([(1490,843),(1490,906)])

box(75,720,330,178,'Máy quản trị',['Đăng nhập user / SSH','Cấu hình và bảo trì'],label='MANAGEMENT')
arrow([(405,802),(800,802),(800,465),(886,465),(886,445)],color='#a79a70')
text(462,732,'SSH 22',20,'#9f8e59',True)
text(462,764,'Nguồn được phép',17,'#9f8e59')

d.line((75,1060,1720,1060),fill='#dce5d7',width=2)
text(75,1084,'Đọc / ghi: Client → Apache/PHP → MySQL → ổ đĩa → trả kết quả về trình duyệt.',23,'#52714f')
text(75,1126,'Chưa có URL/IP thật. HTTPS 443 chỉ bổ sung khi đã cấu hình. Chưa sử dụng reverse proxy riêng.',19,'#8a987d')
text(75,1160,'Sơ đồ này không thay thế minh chứng server, firewall và demo từ máy khách.',19,'#8a987d')
im.save(ROOT/'architecture.png',optimize=True)
print(ROOT/'architecture.png')
