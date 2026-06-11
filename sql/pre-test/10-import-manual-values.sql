INSTALL excel;
LOAD excel;

drop table if exists manual_values_pm;
create table manual_values_pm AS
select *
from read_xlsx('{{manual_values_file}}', header = true, sheet = 'PM');

drop table if exists manual_values_cm;
create table manual_values_cm AS
select *
from read_xlsx('{{manual_values_file}}', header = true, sheet = 'CM');

drop table if exists manual_values_fpsm;
create table manual_values_fpsm AS
select *
from read_xlsx('{{manual_values_file}}', header = true, sheet = 'FPSM');

drop table if exists manual_values_total;
create table manual_values_total AS
select *
from read_xlsx('{{manual_values_file}}', header = true, sheet = 'TOTAL');