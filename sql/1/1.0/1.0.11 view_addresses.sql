CREATE VIEW view_addresses AS
SELECT a.*,ad.name as district,ac.name as city,ap.name as province FROM addresses a
LEFT JOIN address_districts ad ON ad.district_id=a.district_id
LEFT JOIN address_cities ac ON ac.city_id=ad.city_id
LEFT JOIN address_provinces ap ON ap.province_id=ac.province_id