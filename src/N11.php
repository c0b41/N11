<?php

namespace SM\N11;
use SM\N11\N11Exception;
use SoapClient;

class N11 {

  private $appKey;
  private $appSecret;
  private $parameters;
  private $_sclient;
  public $debug = false;

  private $urls = array(
    'CategoryService' => 'https://api.n11.com/ws/CategoryService.wsdl',
    'CityService' => 'https://api.n11.com/ws/CityService.wsdl',
    'ProductService' => 'https://api.n11.com/ws/ProductService.wsdl',
    'OrderService' => 'https://api.n11.com/ws/OrderService.wsdl',
    'ProductStockService' => 'https://api.n11.com/ws/ProductStockService.wsdl'
  );

  function __construct($conf = null) {
    if($this->checkConfig($conf)){
      $this->_parameters = ['auth' => ['appKey' => $this->appKey, 'appSecret' => $this->appSecret]];
    }
  }

  private function setUrl($url){
      $this->_sclient = new SoapClient($url);
  }

  public function GetAllCategories(){

    $toplevelcats = $this->GetTopLevelCategories()->categoryList->category;

    $categorys = [];
  
    function subShow($id, $categorys, $that){

      $result = $that->GetSubCategory($id)->category;
      if(isset($result)){
        if(isset($result->subCategoryList->subCategory)){
          foreach ($result->subCategoryList->subCategory as $subCategory) {
            if(isset($subCategory->name) && isset($subCategory->id)){
              array_push($categorys,array(
                "category_name" => $subCategory->name,
                "category_id" => $subCategory->id,
                "parent_id" => ( isset($id) ? $id : 0)
              ));
            }
            echo 'sub';
            subShow($subCategory->id, $categorys,$that);
          }
        }
      } else {
        return null;
      }
    }

    foreach ($toplevelcats as $cat) {
      array_push($categorys, array(
        "category_name" => $cat->name,
        "category_id" => $cat->id
      ));
      echo 'top';
      subShow($cat->id, $categorys,$this);
    }

    return $categorys;


  }

  public function GetTopLevelCategories() {
      $this->setUrl($this->urls['CategoryService']);
      return (isset($this->_sclient) ? $this->_sclient->GetTopLevelCategories($this->_parameters) : null);

  }

  public function GetCategoryAttributes($id = null){
      $this->setUrl($this->urls['CategoryService']);
      $this->_parameters['categoryId'] = $id;
      return (isset($this->_sclient) ? $this->_sclient->GetCategoryAttributes($this->_parameters) : null);
  }

  public function GetCities() {
      $this->setUrl($this->urls['CityService']);
      return (isset($this->_sclient) ? $this->_sclient->GetCities($this->_parameters) : null);
  }

  public function GetProductList($itemsPerPage, $currentPage) {
      $this->setUrl($this->urls['ProductService']);
      $this->_parameters['pagingData'] = ['itemsPerPage' => $itemsPerPage, 'currentPage' => $currentPage];
      return (isset($this->_sclient) ? $this->_sclient->GetProductList($this->_parameters) : null );
  }

  public function GetProductBySellerCode($sellerCode) {
      $this->setUrl($this->urls['ProductService']);
      $this->_parameters['sellerCode'] = $sellerCode;
      return (isset($this->_sclient) ? $this->_sclient->GetProductBySellerCode($this->_parameters): null );
  }

  public function SaveProduct(array $product = Array()) {
      $this->setUrl($this->urls['ProductService']);
      $this->_parameters['product'] = $product;
      return (isset($this->_sclient) ? $this->_sclient->SaveProduct($this->_parameters) : null );
  }

  public function DeleteProduct($sellerCode) {
      $this->setUrl($this->urls['ProductService']);
      $this->_parameters['productId'] = $sellerCode;
      return (isset($this->_sclient) ? $this->_sclient->DeleteProductById($this->_parameters) : null);
  }

  public function OrderList(array $searchData = Array(
        'status' => 'New',
        'buyerName' => '',
        'orderNumber' => '',
        'recipient' => '',
        'productSellerCode' => '',
        'period' => Array(
            'startDate' => '',
            'endDate' => ''
          )
      ), array $pagingData = Array()) {

      $this->setUrl($this->urls['OrderService']);
      $this->_parameters['searchData'] = $searchData;
      $this->_parameters['pagingData'] = $pagingData;
      return (isset($this->_sclient) ? $this->_sclient->OrderList($this->_parameters) : null );
  }

  public function DetailedOrderList(array $searchData = Array(
        'status' => 'New',
        'buyerName' => '',
        'orderNumber' => '',
        'recipient' => '',
        'productSellerCode' => '',
        'period' => Array(
        'startDate' => '',
        'endDate' => ''
        )
      ), array $pagingData = Array()){
    $this->setUrl($this->urls['OrderService']);
    $this->_parameters['searchData'] = $searchData;
    return (isset($this->_sclient) ? $this->_sclient->DetailedOrderList($this->_parameters) : null );
  }

  public function OrderDetail($id = null){
    $this->setUrl($this->urls['OrderService']);
    $this->_parameters['orderRequest'] = array('id' => $id);
    return (isset($this->_sclient) ? $this->_sclient->OrderDetail($this->_parameters) : null );
  }

  public function GetSubCategory($id = null){
    $this->setUrl($this->urls['CategoryService']);
    $this->_parameters['categoryId'] = $id;
    return (isset($this->_sclient) ? $this->_sclient->GetSubCategories($this->_parameters) : null);
  }

  public function UpdateStockByStockId($params = null){
    $this->setUrl($this->urls['ProductStockService']);
    $this->_parameters['stockItems'] = $params['stockItem'];
    return (isset($this->_sclient) ? $this->_sclient->UpdateStockByStockId($this->_parameters) : null);
  }


  public function __destruct() {
      if ($this->debug) {
          print_r($this->_parameters);
          print_r($this->_sclient->__getLastRequest());
      }
  }

  private function checkConfig($conf){
    if(!isset($conf['app_key']) || !isset($conf['app_secret']) ) {
      throw new N11Exception("N11 Ayarları Girilmedi");
    } else {
      $this->appKey = $conf['app_key'];
      $this->appSecret = $conf['app_secret'];
      return true;
    }
  }
  
}
