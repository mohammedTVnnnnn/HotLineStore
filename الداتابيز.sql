Table users {
  id int [pk, increment]
  name varchar
  email varchar
  password varchar
  role varchar
  created_at datetime
}

Table products {
  id int [pk, increment]
  name varchar
  description text
  price decimal
  stock int
  created_at datetime
}

Table carts {
  id int [pk, increment]
  user_id int [ref: > users.id]
  created_at datetime
}

Table cart_items {
  id int [pk, increment]
  cart_id int [ref: > carts.id]
  product_id int [ref: > products.id]
  quantity int
}

Table invoices {
  id int [pk, increment]
  user_id int [ref: > users.id]
  total decimal
  status varchar
  created_at datetime
}

Table invoice_items {
  id int [pk, increment]
  invoice_id int [ref: > invoices.id]
  product_id int [ref: > products.id]
  quantity int
  price decimal
}
