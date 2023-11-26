# Important things to know

* Firefox always sets cookie secure = true
* Options without value attribute can not be selected by value
  * Session::fillForm and Session::submitForm can therefor not select those options
