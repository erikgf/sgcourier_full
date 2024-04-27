var MainServicio = function() {
    this.initialize = function() {
        return this.compilar();//deferred.promise();
    };

    this.compilar = function(){
      var esAppMovil = document.URL.indexOf( 'http://' ) === -1 && document.URL.indexOf( 'https://' ) === -1;
      var req = esAppMovil ?  Framework7.request.get("template.master.hbs") : Framework7.request.get("template.compiler.php");

      return new Promise(function (resolve, reject) {
		req.onload = function(){
		  if (this.status >= 200 && this.status < 300) {
	        resolve(req.response);
	      } else {
	        reject({
	          status: this.status,
	          statusText: req.statusText
	        });
	      }
		}

		req.onerror = function () {
	      reject({
	        status: this.status,
	        statusText: req.statusText
	      });
	    };
	  });
    };
};

