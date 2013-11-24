WebSys-KISS
===========

A Web Based Kitchen Inventory Supply System

database structure:

users:
name
username
email
password

salts:
name (foreign key)
salt

list:
user (foreign key)
item (foreign key)
category

items:
name
type
picture?