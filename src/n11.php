<?php

namespace SM\N11;
use SM\N11\N11Exception;
use SoapClient;

class N11 {

  private $appKey;
  private $appSecret;
  private $parameters;
  private $client;
  public $debug = false;

  private $urls = array(
    'CategoryService' => 'https://api.n11.com/ws/CategoryService.wsdl',
    'CityService' => 'https://api.n11.com/ws/CityService.wsdl',
    'ProductService' => 'https://api.n11.com/ws/ProductService.wsdl',
    'OrderService' => 'https://api.n11.com/ws/OrderService.wsdl'
  );

  function __construct($conf) {
    if($this->checkConfig($conf)){
      $this->_parameters = ['auth' => ['appKey' => $this->appKey, 'appSecret' => $this->appSecret]];
    }
  }

  private function setUrl($url){
    if($this->checkOnline($url)){
      $this->client = new SoapClient($url);
    } else {
      exit();
    }
  }

  public function GetTopLevelCategories() {
      $this->setUrl($this->urls['CategoryService']);
      return $this->client->GetTopLevelCategories($this->_parameters);
  }

  public function GetCities() {
      $this->setUrl($this->urls['CityService']);
      return $this->client->GetCities($this->_parameters);
  }

  public function GetProductList($itemsPerPage, $currentPage) {
      $this->setUrl($this->urls['ProductService']);
      $this->parameters['pagingData'] = ['itemsPerPage' => $itemsPerPage, 'currentPage' => $currentPage];
      return $this->client->GetProductList($this->parameters);
  }

  public function GetProductBySellerCode($sellerCode) {
      $this->setUrl($this->urls['ProductService']);
      $this->parameters['sellerCode'] = $sellerCode;
      return $this->client->GetProductBySellerCode($this->_parameters);
  }

  public function SaveProduct(array $product = Array()) {
      $this->setUrl($this->urls['ProductService']);
      $this->parameters['product'] = $product;
      return $this->client->SaveProduct($this->_parameters);
  }

  public function DeleteProductBySellerCode($sellerCode) {
      $this->setUrl($this->$urls['ProductService']);
      $this->$parameters['productSellerCode'] = $sellerCode;
      return $this->$client->DeleteProductBySellerCode($this->$_parameters);
  }

  public function OrderList(array $searchData = Array()) {
      $this->setUrl($this->$urls['OrderService']);
      $this->$parameters['searchData'] = $searchData;
      return $this->$client->OrderList($this->$_parameters);
  }

  public function GetSubCategory($id = null){
    $this->setUrl($this->urls['CategoryService']);
    $this->parameters['categoryId'] = $id;
    return $this->client->GetSubCategories($this->_parameters);
  }

  public function __destruct() {
      if ($this->debug) {
          print_r($this->$parameters);
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

  private function checkOnline($domain) {
     $curlInit = curl_init($domain);
     curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
     curl_setopt($curlInit,CURLOPT_HEADER,true);
     curl_setopt($curlInit,CURLOPT_NOBODY,true);
     curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
     $response = curl_exec($curlInit);
     curl_close($curlInit);
     if ($response) return true;
     return false;
  }

  function display(){
    return 'dfgdfg';
  }
}
