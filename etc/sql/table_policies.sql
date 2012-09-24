use ts_lig;

alter table policies
add job_id int null
after homephone;

alter table policies
add package_id int null
after gender;

alter table policies
add relationship_code int null
after birth_place;

alter table customers
add package_id int null
after subtotal_premium;

