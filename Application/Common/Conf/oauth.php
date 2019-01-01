<?php

//define('URL_CALLBACK', 'http://www.zefengdaguo.com/login/callback/type/');
define('URL_CALLBACK', "http://test.shihewang.com/ucent/login/callback/type/");
return array(

    //腾讯QQ登录配置
    'THINK_SDK_QQ' => array(
        'APP_KEY' => '101450301', //应用注册成功后分配的 APP ID
        'APP_SECRET' => 'c770a70fa63f4e826c2d7e50a8bca093', //应用注册成功后分配的KEY
        'CALLBACK' => URL_CALLBACK . 'qq.html',
    ),
    //新浪微博配置
    'THINK_SDK_SINA' => array(
        'APP_KEY' => '1815768815', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '7fd15ca9bcce4945e1881fdac16a23b5', //应用注册成功后分配的KEY
        'CALLBACK' => URL_CALLBACK . 'sina.html',
    ),
    //人人网配置
    'THINK_SDK_RENREN' => array(
        'APP_KEY' => '', //应用注册成功后分配的 APP ID
        'APP_SECRET' => '', //应用注册成功后分配的KEY
        'CALLBACK' => URL_CALLBACK . 'renren.html',
    ),
      //支付宝
    'THINK_SDK_ALI' => array(
        'requestcodeurl'=>'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm',
        'gatewayUrl' => 'https://openapi.alipay.com/gateway.do', //应用注册成功后分配的 APP ID
        'app_id' => '2018012302043469', //应用注册成功后分配的KEY
        'merchant_private_key'=>'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCVHfhLMFQbljF3RgkwwyGzd691QaFCW/cgFVTI9sOQZMlTBv50Spg0Dr1F/oFGtW1wRnsgN4BhozdWtj5O0P4Ak9UK7rFqG39LbgGStOElWzfdrJ/xaDpxw1Jcsh6qzbd2zDhPN+AnKOidPrW8QfCrsGOvASi1MC0HX71peXeCq2VW3+NwqfkfdrWOQ9n2TGj9gtpoai4vOIFeZxoUKBMJhE0lFJz9O0teH0iJKpKMwa1lxfZWEltuj/124WrnTXhcU/jRep1F4nkote1BGchIkEGarKLw7FnXyP9K4yqyqtnkN9PX4mlg6iQhItUF070dUsMm86CmYmq3uBYySQBtAgMBAAECggEATyh3tEJVg8d5Pt3Y5x/O9hxk8hQC7N0xFMD+HyiPrEk0bnTaAxXOMmRYqZ1vNv/8totTtUiBSFOCfu+DqAddryTLBWCsoa0zJ/GfuCd5FYGU6IJI7bfsvpN3V2neycEm0VOb2MHKpj8bLM4rX2KJxjp+0FoHKBgI708tl545iQ1voCOv9OZeFxhD3rEvnJnX8Av0Z/1Rv/SmcY4K0g+ahndVWaRY91q/AWSLYpWVo+FLy+GpVznqNPPVQFB+WJeWJKtcZCZFIWIl9Kybhs0iKS18eoTiVkJwHeQWxrjqePk8Mj9aY6CCcORe1n87Yn7S8ae5Zhz2zasoXnORBRlowQKBgQDOar9fRsrSySAe0nc1SnLkwtlGB2ray0p5l0ZWyiiCUqvaZg/TlUN6QDHlJCWhKnWUTJUuWiJghMKg0YoyMOaC9/FmPZNyTasP+LT32F4upkptSu5JA32lCjQi/ousELBxH2JSZHASigL7Q525GYjCVOcn31sIv5k3cOcJykXuRQKBgQC476oD6pn5IYgPUQzWBL5cJCV6RYXiT60rkVlU7S0FejaI1/7WLrx5zPkcpG1bguNjzqsViRepNqsKaCofyfJZ81z1J6uVo9mUuotTP4zWaYq08yZR1ujxHEGhuBYVhpASPMtnu754FttFN+VQa4xToAhGTj7UEvsiZjVJ+g8gCQKBgQDHxkCZMaazArz3l7KP/RHdnUczo5IjV4Uo0OPes6EpIGH66bR/joLUGrUvlCBnLvTFJqlrRwjmaRcFFket8+3k65rS3N2LTug0ePyMsvfUJlT6Gf8s4xgDVzDGfZ0oVq6tjTZXj6V4C+J+1B4eOzxE+G8QA/fTK7xaEAdLTl0IEQKBgDCgU8PT28h12KCuXwGnHaZ8mp4byahH6vCZtzNtaXkXOV+h64/FmiksjBPL6DU3pTKJFNMEyQPlK/QAj5LsNp0QMFEFrUgbXQqOQjQRcaau+Zm6lUjPiVBcbaVoEeZ3C0rAUXtlEh/hYqZAnDsZDDywx3w09rAsjXvuA/T/mxqBAoGBALFt2OvEA2xOnEWIsm2V3x7lEqGvn2deTFQof88Q17O3eeeyCM4RTU2k1LALk97YzhPCrC3xMrNebs8beP4UP4Ko1AgEtCVkEoe2MD382SfvOXSp1nSucDjC1BR5ecW4DiVohfROFmofw4Bq+c+HCFWWYpqSMWp1IrrxiRWYmRRu',// '请填写开发者私钥去头去尾去回车，一行字符串';
        'alipay_public_key'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAhxkgTrDchUp6W5c05S4vUEynp8DIWi2eMFk+x8yF4wL1GiJsc6+6t9TVCLv05sRn+C5z0vD33/rtOWfpIhujDvXTPjPFtU98IpI1wR//+k94WCYjz7K6p+wVXqIuX7JHeJ7YfKfZCYqwcPkvzZ9mT1gtz8NYRNSFNuf2WPFXNZcSu+RbmOzjKlS0+nM/2VlZjK20zLf4xSajtVy1bg3Fy+N8bZr5gQDfzTjxFiQP0LYjZVrd6oSpVZ/87LOfywXMKJSuM+U5gR5EY4/gnHeWg8kSANDjwXx4esf2lcKXN8ypGgcfF4lGTSoI545DmXU/ZOEs+a9gi1QZn2tM9MZKkQIDAQAB',// '请填写支付宝公钥，一行字符串';
        'redirect_uri' => URL_CALLBACK . 'ali.html',
    ),
    //支付宝沙箱
    'THINK_SDK_ALI_SX' => array(
        'requestcodeurl'=>'https://openauth.alipaydev.com/oauth2/publicAppAuthorize.htm',
        'gatewayUrl' => 'https://openapi.alipaydev.com/gateway.do', //应用注册成功后分配的 APP ID
        'app_id' => '2016091300500590', //应用注册成功后分配的KEY
        'merchant_private_key'=>'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCVHfhLMFQbljF3RgkwwyGzd691QaFCW/cgFVTI9sOQZMlTBv50Spg0Dr1F/oFGtW1wRnsgN4BhozdWtj5O0P4Ak9UK7rFqG39LbgGStOElWzfdrJ/xaDpxw1Jcsh6qzbd2zDhPN+AnKOidPrW8QfCrsGOvASi1MC0HX71peXeCq2VW3+NwqfkfdrWOQ9n2TGj9gtpoai4vOIFeZxoUKBMJhE0lFJz9O0teH0iJKpKMwa1lxfZWEltuj/124WrnTXhcU/jRep1F4nkote1BGchIkEGarKLw7FnXyP9K4yqyqtnkN9PX4mlg6iQhItUF070dUsMm86CmYmq3uBYySQBtAgMBAAECggEATyh3tEJVg8d5Pt3Y5x/O9hxk8hQC7N0xFMD+HyiPrEk0bnTaAxXOMmRYqZ1vNv/8totTtUiBSFOCfu+DqAddryTLBWCsoa0zJ/GfuCd5FYGU6IJI7bfsvpN3V2neycEm0VOb2MHKpj8bLM4rX2KJxjp+0FoHKBgI708tl545iQ1voCOv9OZeFxhD3rEvnJnX8Av0Z/1Rv/SmcY4K0g+ahndVWaRY91q/AWSLYpWVo+FLy+GpVznqNPPVQFB+WJeWJKtcZCZFIWIl9Kybhs0iKS18eoTiVkJwHeQWxrjqePk8Mj9aY6CCcORe1n87Yn7S8ae5Zhz2zasoXnORBRlowQKBgQDOar9fRsrSySAe0nc1SnLkwtlGB2ray0p5l0ZWyiiCUqvaZg/TlUN6QDHlJCWhKnWUTJUuWiJghMKg0YoyMOaC9/FmPZNyTasP+LT32F4upkptSu5JA32lCjQi/ousELBxH2JSZHASigL7Q525GYjCVOcn31sIv5k3cOcJykXuRQKBgQC476oD6pn5IYgPUQzWBL5cJCV6RYXiT60rkVlU7S0FejaI1/7WLrx5zPkcpG1bguNjzqsViRepNqsKaCofyfJZ81z1J6uVo9mUuotTP4zWaYq08yZR1ujxHEGhuBYVhpASPMtnu754FttFN+VQa4xToAhGTj7UEvsiZjVJ+g8gCQKBgQDHxkCZMaazArz3l7KP/RHdnUczo5IjV4Uo0OPes6EpIGH66bR/joLUGrUvlCBnLvTFJqlrRwjmaRcFFket8+3k65rS3N2LTug0ePyMsvfUJlT6Gf8s4xgDVzDGfZ0oVq6tjTZXj6V4C+J+1B4eOzxE+G8QA/fTK7xaEAdLTl0IEQKBgDCgU8PT28h12KCuXwGnHaZ8mp4byahH6vCZtzNtaXkXOV+h64/FmiksjBPL6DU3pTKJFNMEyQPlK/QAj5LsNp0QMFEFrUgbXQqOQjQRcaau+Zm6lUjPiVBcbaVoEeZ3C0rAUXtlEh/hYqZAnDsZDDywx3w09rAsjXvuA/T/mxqBAoGBALFt2OvEA2xOnEWIsm2V3x7lEqGvn2deTFQof88Q17O3eeeyCM4RTU2k1LALk97YzhPCrC3xMrNebs8beP4UP4Ko1AgEtCVkEoe2MD382SfvOXSp1nSucDjC1BR5ecW4DiVohfROFmofw4Bq+c+HCFWWYpqSMWp1IrrxiRWYmRRu',// '请填写开发者私钥去头去尾去回车，一行字符串';
        'alipay_public_key'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAy2IdXpCKDqLx/ElHXBGIAa3J7DEfQd9y5lqKDZBVO9A9i/88syD+C5+bn4yb5rx5hFIWHmSx/dy/fIATczRnC+tefsTmHEQ2XPuB1UyfvL08I5TlZsaY67CzmtnnRdENtQb/Cu0d1mEsZyUlp67IqhdMDnexFrgOcQ/mfIYDE9ohn2PIQeA9seoTIk1+siuew/e6HlqXZh3Th9UKHYhZiosaVejRZOvA2v3bXEa7Yrn4vgI3V0G5SAtzB06KeNvL71dRo71qBKbEAWmobYnDOwi8SF5kw6hoc73Yp7YL6f6YOV55SnTJNOIwSSK9hpKUHeChtvQSCaEFMwAbpi1y+wIDAQAB',// '请填写支付宝公钥，一行字符串';
        'redirect_uri' => URL_CALLBACK . 'ali.html',
    ),
);
