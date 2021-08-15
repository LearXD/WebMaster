<h1 align="center"> WebMaster v0.1 </h1>
<p align="center"> A small REST query library for PocketMine-MP </p>

<h2 align="center"> Types of Query: </h1>


<div align="center">
  
- [x] GET
- [x] POST
- [ ] PUT
- [ ] PATCH
- [ ] DELETE
- [ ] OPTIONS
- [ ] HEAD

  
METHOD  | EXAMPLE
------------- | -------------
GET  | []
POST  | ['field1' => 'data', 'field2' => 'data2']
  
</div>

<h2 align="center"> 🔨 Usage: </h1>

<div align="center">
  
  <b>The WebMaster class receives 3 parameters as a constructor function, first the address, according to the query context (which receives the query result as a parameter), and finally the array with fields.</b>
  
</div>

<ul>
  <li> How to correctly call the class: </li>
</ul>

```php
$query = new \your\directory\WebMaster('https://localhost', function($result) { var_dump($request); }, [/*WHAT COMES HERE IS REGARDING THE TABLE ABOVE...*/]);
```
