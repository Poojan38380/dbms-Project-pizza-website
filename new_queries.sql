ALTER TABLE products ADD regular_price int(255);
ALTER TABLE products ADD medium_price int(255);
ALTER TABLE products ADD large_price int(255);
ALTER TABLE products ADD category VARCHAR(100);
ALTER TABLE cart ADD crust varchar(225),toppings varchar(225) DEFAULT NULL, size varchar(225) DEFAULT NULL;
  ALTER TABLE cart ADD category VARCHAR(255);

