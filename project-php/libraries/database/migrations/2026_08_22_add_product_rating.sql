ALTER TABLE `table_product`
  ADD COLUMN IF NOT EXISTS `rating` DECIMAL(3,1) NOT NULL DEFAULT '5.0' AFTER `sale_price`,
  ADD COLUMN IF NOT EXISTS `rating_average` DECIMAL(3,1) NOT NULL DEFAULT '5.0' AFTER `rating`,
  ADD COLUMN IF NOT EXISTS `rating_count` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `rating_average`;

UPDATE `table_product` AS p
LEFT JOIN (
  SELECT id_variant, type, COUNT(*) AS total, ROUND(AVG(star), 1) AS average
  FROM `table_comment`
  WHERE id_parent = 0 AND type = 'san-pham' AND star BETWEEN 1 AND 5 AND FIND_IN_SET('hienthi', status)
  GROUP BY id_variant, type
) AS c ON c.id_variant = p.id AND c.type = p.type
SET p.rating_count = COALESCE(c.total, 0),
    p.rating_average = CASE WHEN c.total > 0 THEN c.average ELSE p.rating END
WHERE p.type = 'san-pham';
