ALTER TABLE products DROP COLUMN price;
ALTER TABLE products ADD regular_price int(255);
ALTER TABLE products ADD medium_price int(255);
ALTER TABLE products ADD large_price int(255);
