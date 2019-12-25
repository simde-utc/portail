# Partner

Partners are compagnies which offer discounts for students in exchange of visibility on our website.

### Attributes
- `name` : Name of the compagny
- `description` : Details of discount
- `image` : Link (local or not) to the image/logo of the compagny
- `website` : Link to the compagny's website
- Address : 
  - `address` : Street Number + Street Name
  - `postal_code` : ZIP code of the company's city
  - `city` : Explicit
- `created_at` : Handled by Eloquent
- `updated_at` : Handled by Eloquent
  
## Administration
Administrators with the right rights can update every field described above but Eloquent-handled fields and create/delete a partner. For the address, if one of the three fields is `NULL`, the addrress won't be displayed in the partners front page.

## Selection

- `'paginate => 10` : The number of partners for each page is 10.
- Partners are by default with an order based on the latest added partner (it is based on the `created_at` fiels). If the order is based on columns, the order will be based on the `name` attribute

