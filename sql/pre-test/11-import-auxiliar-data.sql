INSTALL excel;
LOAD excel;

drop table if exists auxiliar_niveis;
create table auxiliar_niveis AS
select *
from read_xlsx('auxiliar_data.xlsx', header = true, sheet = 'niveis');

drop table if exists auxiliar_saldo_invertido;
create table auxiliar_saldo_invertido AS
select *
from read_xlsx('auxiliar_data.xlsx', header = true, sheet = 'saldo_invertido');
