# TindaPress - WordPress Plugin

The TindaPress for WordPress is an e-commerce plugin lorem ipsum

### REST API Implementation

####  INSERT STORE

REQUEST:
- Method: POST
- Header: key=Content-Type, value=application/x-www-form-urlencoded
- Params: key=snky (value=session key), key=ctid (value=Category ID), key=title (value= title of store), key = short_info  (Value = short information of product ), key = long_info (Value = long info of product ), key = logo (Value = logo of product ), key = banner (Value = banner of product ), key = add (Value = address id of product )

RESPONSE:
- code: 'unknown' message: 'Please contact your administrator. Plugin Missing!' data: null
- code: 'unknown' message: 'Please contact your administrator. Request Unknown!' data: null
- code: 'unknown' message: 'Please contact your administrator.  Request unknown missing parameters!' data: null
- code: 'failed' message: 'Please contact your administrator. ID not in valid format!' data: null
- code: 'unknown' message: 'Required fields cannot be empty' data: null
- code: 'failed' message: 'Address not found' data: null

IF SUCCESS:
```
    
```

#### INSERT STORE

REQUEST:
- Method: GET
- Header: key=Content-Type, value=application/x-www-form-urlencoded
- Params: key=wpid (value=User ID), key=session key (value=User snky), key=stid (value = Store id), key=title (value = title of product ), key = preview (value = preview of product ), key=short_info (value = short info of product ), key= long_info (value = long info of product ), key=sku (value = stock keeping unit ), key= price (value = price ), key= weight (value = weight ), key= dimension (value = dimension )
RESPONSE:
- code: 'unknown' message: 'Please contact your administrator. Plugin Missing!' data: null
- code: 'unknown' message: 'Please contact your administrator. Request Unknown!' data: null
- code: 'unknown' message: 'Please contact your administrator.  Request unknown missing parameters!' data: null
- code: 'failed' message: 'Please contact your administrator. ID not in valid format!' data: null
- code: 'unknown' message: 'Required fields cannot be empty' data: null
- code: 'failed' message: 'An error occured while submiting data to server' data: null

IF SUCCESS:
```
  
```

#### UPDATE PRODUCT

REQUEST:
- Method: GET
- Header: key=Content-Type, value=application/x-www-form-urlencoded
- Params: key=wpid (value=User ID), key=session key (value=User snky), key=stid (value = Store id), key=title (value = title of product ), key = preview (value = preview of product ), key=short_info (value = short info of product ), key= long_info (value = long info of product ), key=sku (value = stock keeping unit ), key= price (value = price ), key= weight (value = weight ), key= dimension (value = dimension ), key = prod_id (Value = product id)
RESPONSE:
- code: 'unknown' message: 'Please contact your administrator. Plugin Missing!' data: null
- code: 'unknown' message: 'Please contact your administrator. Request Unknown!' data: null
- code: 'unknown' message: 'Please contact your administrator.  Request unknown missing parameters!' data: null
- code: 'failed' message: 'Please contact your administrator. ID not in valid format!' data: null
- code: 'unknown' message: 'Required fields cannot be empty' data: null
- code: 'failed' message: 'Product not found' data: null
- code: 'failed' message: 'Category not found' data: null

IF SUCCESS:
```
  
```


#### SELECT SINGLE PRODUCT

REQUEST:
- Method: GET
- Header: key=Content-Type, value=application/x-www-form-urlencoded
- Params: key=snky (value=session key), key=wpid (value=wordpress id), key = prod_id (Value = product id)
RESPONSE:
- code: 'unknown' message: 'Please contact your administrator. Plugin Missing!' data: null
- code: 'unknown' message: 'Please contact your administrator. Request Unknown!' data: null
- code: 'unknown' message: 'Please contact your administrator.  Request unknown missing parameters!' data: null
- code: 'failed' message: 'Please contact your administrator. ID not in valid format!' data: null
- code: 'unknown' message: 'Required fields cannot be empty' data: null
- code: 'failed' message: 'Product not found' data: null

IF SUCCESS:
```
  
```

#### LIST ALL PRODUCT

REQUEST:
- Method: GET
- Header: key=Content-Type, value=application/x-www-form-urlencoded
- Params: key=snky (value=session key), key=wpid (value=wordpress id)
RESPONSE:
- code: 'unknown' message: 'Please contact your administrator. Plugin Missing!' data: null
- code: 'unknown' message: 'Please contact your administrator. Request Unknown!' data: null
- code: 'unknown' message: 'Please contact your administrator.  Request unknown missing parameters!' data: null
- code: 'failed' message: 'Please contact your administrator. ID not in valid format!' data: null
- code: 'unknown' message: 'Required fields cannot be empty' data: null

IF SUCCESS:
```
  
  
```




## Contributing 

Please read [CONTRIBUTING](CONTRIBUTING) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/BytesCrafter/DataVice-WP-Plugin/tags). 

## Authors

* **Bytes Crafter** - *Initial work* - [Github](https://github.com/BytesCrafter/DataVice-WP-Plugin.git)

See also the list of [contributors](https://github.com/BytesCrafter/DataVice-WP-Plugin/graphs/contributors) who participated in this project.

## License

This project is licensed under the GNU GPL License - see the [LICENSE](LICENSE) file for details

## Acknowledgments

* WordPress.Org
* MariaDB Developer

<!-- Test Now -->