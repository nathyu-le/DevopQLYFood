-- Optional sample content, not evidence of a server deployment.
-- Import once into a fresh database after schema.sql; repeated imports add rows.
SET NAMES utf8mb4;
INSERT INTO dishes (name, category, price, description, is_available) VALUES
('Cơm gà Hội An', 'main', 45000, 'Cơm gà xé, rau răm và nước dùng nóng.', 1),
('Bún bò Huế', 'main', 55000, 'Nước dùng sả thơm, bắp bò và chả.', 1),
('Gỏi cuốn tôm thịt', 'starter', 35000, 'Cuốn tươi dùng kèm tương đậu phộng.', 1),
('Chả giò giòn', 'starter', 40000, 'Chả giò nhân thịt, ăn cùng rau sống.', 1),
('Trà đào cam sả', 'drink', 30000, 'Trà đào, cam tươi và sả.', 1),
('Nước chanh tươi', 'drink', 20000, 'Chanh tươi, điều chỉnh đường khi phục vụ.', 1),
('Chè hạt sen', 'dessert', 25000, 'Hạt sen nấu đường phèn.', 0),
('Sữa chua trái cây', 'dessert', 28000, 'Sữa chua cùng trái cây theo mùa.', 1);
