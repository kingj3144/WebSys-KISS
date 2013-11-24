WebSys-KISS
===========

A Web Based Kitchen Inventory Supply System

database structure:

users:
name
password

salts:
name
salt

list:
user (foreign key)
item (foreign key)
category

items:
name
type
picture?