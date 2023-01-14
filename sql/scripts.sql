-- public
select a.id "article_id",
       a.name_ "article_name",
       description_ "description",
       rating,
       year,
       supplier_price,
       image_ "image",
       s.name_ "supplier_name",
       quantity
from article a
         inner join supplier s on a.supplier_id = s.id
         inner join stock s2 on a.id = s2.article_id
where a.id = 1;

select a.id "article_id",
    a.name_ "article_name",
    description_ "description",
    rating,
    year,
    supplier_price,
    image_ "image",
    s.name_ "supplier_name",
    quantity
from article a
        inner join supplier s on a.supplier_id = s.id
        inner join stock s2 on a.id = s2.article_id
order by a.id
limit 0, 2;


select *
from article
order by id
limit 1, 20;


-- Utilisateurs

-- voir si un compte existe deja
select count(email) "nb" from client where email = '';

-- Créer un nouvel utilisateur
insert into client (email, password_, last_name, first_name, admin_)
values ();

-- Session utilisateur
insert into session (client_email, token, expires)
    values ('raph10@mail.com', 'token', addtime(now(), '1 0:10'));

select * from session order by expires desc;

delete from session where expires < now();

delete from session
       where client_email = (
           select client_email from session
           where token = ''
           );

-- Utilisateur admin ?
select admin_ from client c
inner join session s on c.email = s.client_email
where token = '';

-- verif utilisateur
select email,
       last_name,
       first_name,
       password_ "password",
       admin_ "admin"
from client
where email = 'raph10@mail.com';

-- verification des stocks
select article_id, quantity from stock where quantity < 10;

-- Administration
insert into supplier (name_)
values ('Samsung'),
       ('Apple'),
       ('Xiaomi'),
       ('LG'),
       ('Nokia');

select id, name_ "name"
from supplier where upper(name_) = upper('samsung');

insert into article (supplier_id, name_, description_, rating, year, supplier_price)
VALUES (1, 'Galaxy S20', 'Un teléphone incroyable...', 5, 2021, 800),
       (1, 'Galaxy S22', 'Un telne incroyable...', 5, 2071, 880),
       (1, 'Galaxy S21', 'Un teléphonable...', 5, 2020, 900),
       (2, 'Iphone X', 'Un teléphone incroyable...', 4, 2021, 1000),
       (2, 'Iphone 2', 'Un teléphone...', 2, 2021, 700);

insert into stock (article_id, quantity)
VALUES (1, 200);

-- récuperer le stock d'un article
select quantity from stock where article_id = 7;

-- modifier le stock d'un article
update stock
set quantity = 30
where article_id = 7;

select quantity
from stock
where article_id = 7;


select auto_increment - 1 "id"
from information_schema.TABLES
where TABLE_SCHEMA = database() and TABLE_NAME = 'supplier';

select last_insert_id(); -- voir ce que renvoie PDO pour créer le stock (id)


-- Supprimer un article + son stock
delete from article
where id = 5;

-- panier
select id from cart
where id not in (
    select c.id from cart c
    inner join invoice i on c.id = i.cart_id
    where c.client_email = 'raph10@mail.com'
    );

-- créer un nouveau panier
insert into cart (client_email) values ('raph10@mail.com');

-- Les articles du panier
select article_id, quantity
from cart_article
where cart_id = 7;

select count(*) from cart_article
    where cart_id = 1 and article_id = 7;

insert into cart_article (cart_id, article_id, quantity)
values (1, 2, 2);

update cart_article
set quantity = 7
where cart_id = 1 and article_id = 7;

delete from cart_article
where cart_id = 1 and article_id = 7;

-- des infos detailés sur un panier
select a.id as "article_id",
       a.name_ "article_name",
       description_ "description",
       rating,
       year,
       supplier_price * 1.08 "base_price",
       image_ "image",
       s.name_ "supplier_name",
       s2.quantity "stock_quantity",
       ca.quantity "cart_quantity",
       ca.quantity * supplier_price * 1.08 "price_no_tax",
       ca.quantity * supplier_price * 1.08 * 1.2 "price_tax"
from article a
         inner join supplier s on a.supplier_id = s.id
         inner join stock s2 on a.id = s2.article_id
         inner join cart_article ca on a.id = ca.article_id
where a.id in (
    select article_id from cart_article
                      where cart_id = 7
    );

-- supprimer le panier
delete from cart where id = 1;

-- commander le panier
insert into invoice (cart_id, created)
values (0, now());

-- liste des factures précédentes
select i.id "invoice_id",
       i.cart_id,
       i.created,
       (select sum(quantity) from cart_article
           where cart_id = i.cart_id) "num_articles"
from invoice i
inner join cart c on i.cart_id = c.id
where c.client_email = 'raph10@mail.com';

-- liste des paniers précedents
select c.id from cart c
inner join invoice i on c.id = i.cart_id
where client_email = 'raph10@mail.com';

-- infos détaillées sur un panier commandé (les articles du panier)
select a.id as "article_id",
       a.name_ "article_name",
       description_ "description",
       rating,
       year,
       supplier_price,
       image_ "image",
       s.name_ "supplier_name",
       ca.quantity "cart_quantity",
       ca.quantity * supplier_price "price_no_tax",
       ca.quantity * supplier_price * 1.2 "price_tax"
from article a
         inner join supplier s on a.supplier_id = s.id
         inner join cart_article ca on a.id = ca.article_id
         inner join cart c on ca.cart_id = c.id
         inner join invoice i on c.id = i.cart_id
where c.id = 7;

select id from invoice
where cart_id = 8;

-- Les ventes par factures
select i.id,
       count(ca.article_id) * ca.quantity "sales"
from invoice i
inner join cart c on i.cart_id = c.id
inner join cart_article ca on c.id = ca.cart_id
group by i.id;

-- Chiffre d'affaire (turnover)
select sum(a.supplier_price * ca.quantity * 0.08)
from invoice i
inner join cart c on i.cart_id = c.id
inner join cart_article ca on c.id = ca.cart_id
inner join article a on ca.article_id = a.id;

-- Les produits les plus vendus + chiffre d'affaire
select ca.article_id,
       sum(ca.quantity) as total_quantity,
       a.supplier_price * 0.08 * sum(ca.quantity) turnover
from invoice i
inner join cart c on i.cart_id = c.id
inner join cart_article ca on c.id = ca.cart_id
inner join article a on ca.article_id = a.id
group by ca.article_id
order by total_quantity desc;

-- les 10 articles les plus en stock
select name_ name, article_id, quantity from stock
inner join article a on stock.article_id = a.id
order by quantity desc
limit 10;

-- nombre d'articles par fournisseurs
select s.name_ "supplier_name",
       count(*) "num_articles"
from article
inner join supplier s on article.supplier_id = s.id
group by s.id;

-- bref
select i.id as                        "id",
       (select sum(quantity) from cart_article
           where cart_id = i.cart_id) "num_articles"
from invoice i
inner join cart c on i.cart_id = c.id
where c.id = 4;
