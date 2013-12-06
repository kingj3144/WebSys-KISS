#KISS Documentation

This document can be referenced in order to understand the structure for the KISS system.

=====

Table of Contents

1. [Introduction](#Introduction)
2. [Usage](#Usage)
3. [Installation](#Installation)
4. [Database Structure](#Database Structure)
5. [Classes](#Classes)



#Introduction
KISS is a lightweight inventory management system, specifically designed for grocery shopping.

This build consists of a web application which allows users to sign up for accounts, create lists, add items to the lists, while applying simple attributes to said items, and share these lists with other users.

KISS is built using PHP as it's main web technology, using several functions to complete different tasks. It also uses javascript and the Twitter Bootstrap library.

NOTE: This current version only implements Twitter Boostrap v2.3.2, plans to update to v3.0.2 are in place.

#Usage

The purpose of this software is to provide users with a way to create lists. It is used in a web environment, preferrably something that supports HTML5 and Javascript.


#Installation

Installation is simple, the database should automatically setup upon creating the first user.




#Classes

KissDatabase:

Functions:

connect() - connect to DB

close() - close connection

init() - start DB

getUserByName(name) - finds a users and returns if they exist.

getSaltByUser(name) - returns a user's salt

addUser(addUser) - create a new user in the DB

hashPassword(password, salt) - return the crypt

verifyUser(user, password) - verify a user's connection

removeUser(name, listname) - remove a user and all owned lists

deletelist(listid) - remove a list

getListByName(username, listname) - return desired list

addUserToList(listid, username) - add a user to a list

checkUserAccess(listid, username) - check if a user has access to a list

removeUserAccess(listid, username) - remove a user from a list

addItemToList(username, item, listid, category, quantity, unit) - add an item to a list

removeItemFromList(itemid) - remove an item from a list

getItemsFromList(listid) - return items in a list

getListsFromUser(username) - return all lists owned by a user

getListName(listid) - return the name of a list

getAccessList(listid) - return all users with access to a list

updateEmail(username, email) - update user's email

updateName(username, name) - update user's name

isOwner(username, listid) - check if user is owner of a list
