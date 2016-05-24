angular.module("dias.api",["ngResource"]),angular.module("dias.api").config(["$httpProvider","$compileProvider",function(e,t){"use strict";e.defaults.headers.common["X-Requested-With"]="XMLHttpRequest",t.debugInfoEnabled(!1)}]),angular.module("dias.ui.messages",["ui.bootstrap"]),angular.module("dias.ui.messages").config(["$compileProvider",function(e){e.debugInfoEnabled(!1)}]),angular.element(document).ready(function(){"use strict";angular.bootstrap(document.querySelector('[data-ng-controller="MessagesController"]'),["dias.ui.messages"])}),angular.module("dias.ui.users",["ui.bootstrap","dias.api"]),angular.module("dias.ui.users").config(["$compileProvider",function(e){"use strict";e.debugInfoEnabled(!1)}]),angular.module("dias.ui.utils",[]),angular.module("dias.ui.utils").config(["$compileProvider",function(e){"use strict";e.debugInfoEnabled(!1)}]),angular.module("dias.ui",["ui.bootstrap","dias.ui.messages","dias.ui.users","dias.ui.utils","ngAnimate"]),angular.module("dias.ui").config(["$compileProvider",function(e){"use strict";e.debugInfoEnabled(!1)}]),angular.module("dias.api").factory("Annotation",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/annotations/:id",{id:"@id"},{save:{method:"PUT"},query:{method:"GET",url:t+"/api/v1/images/:id/annotations",isArray:!0},add:{method:"POST",url:t+"/api/v1/images/:id/annotations"}})}]),angular.module("dias.api").factory("AnnotationLabel",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/annotation-labels/:id",{id:"@id",annotation_id:"@annotation_id"},{query:{method:"GET",url:t+"/api/v1/annotations/:annotation_id/labels",isArray:!0},attach:{method:"POST",url:t+"/api/v1/annotations/:annotation_id/labels"},save:{method:"PUT",params:{annotation_id:null}},"delete":{method:"DELETE",params:{annotation_id:null}}})}]),angular.module("dias.api").factory("Image",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/images/:id",{id:"@id"})}]),angular.module("dias.api").factory("Label",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/labels/:id",{id:"@id"},{add:{method:"POST"},save:{method:"PUT"}})}]),angular.module("dias.api").factory("MediaType",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/media-types/:id",{id:"@id"})}]),angular.module("dias.api").factory("OwnUser",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/users/my",{},{save:{method:"PUT"}})}]),angular.module("dias.api").factory("Project",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/projects/:id",{id:"@id"},{query:{method:"GET",params:{id:"my"},isArray:!0},add:{method:"POST"},save:{method:"PUT"}})}]),angular.module("dias.api").factory("ProjectLabel",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/projects/:project_id/labels",{project_id:"@project_id"})}]),angular.module("dias.api").factory("ProjectTransect",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/projects/:project_id/transects/:id",{id:"@id"},{add:{method:"POST"},attach:{method:"POST"},detach:{method:"DELETE"}})}]),angular.module("dias.api").factory("ProjectUser",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/projects/:project_id/users/:id",{id:"@id"},{save:{method:"PUT"},attach:{method:"POST"},detach:{method:"DELETE"}})}]),angular.module("dias.api").factory("Role",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/roles/:id",{id:"@id"})}]),angular.module("dias.api").factory("Shape",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/shapes/:id",{id:"@id"})}]),angular.module("dias.api").factory("Transect",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/transects/:id",{id:"@id"},{save:{method:"PUT"}})}]),angular.module("dias.api").factory("TransectImage",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/transects/:transect_id/images",{},{save:{method:"POST",isArray:!0}})}]),angular.module("dias.api").factory("User",["$resource","URL",function(e,t){"use strict";return e(t+"/api/v1/users/:id/:query",{id:"@id"},{save:{method:"PUT"},add:{method:"POST"},find:{method:"GET",params:{id:"find"},isArray:!0}})}]),angular.module("dias.api").service("roles",["Role",function(e){"use strict";var t={},i={};e.query(function(e){e.forEach(function(e){t[e.id]=e.name,i[e.name]=e.id})}),this.getName=function(e){return t[e]},this.getId=function(e){return i[e]}}]),angular.module("dias.api").service("shapes",["Shape",function(e){"use strict";var t={},i={},a=e.query(function(e){e.forEach(function(e){t[e.id]=e.name,i[e.name]=e.id})});this.getName=function(e){return t[e]},this.getId=function(e){return i[e]},this.getAll=function(){return a}}]),angular.module("dias.ui.users").directive("userChooser",function(){"use strict";return{restrict:"A",scope:{select:"=userChooser"},replace:!0,template:'<input type="text" data-ng-model="selected" data-uib-typeahead="name(user) for user in find($viewValue)" data-typeahead-wait-ms="250" data-typeahead-on-select="select($item)"/>',controller:["$scope","User",function(e,t){e.name=function(e){return e&&e.firstname&&e.lastname?e.firstname+" "+e.lastname:""},e.find=function(e){return t.find({query:encodeURIComponent(e)}).$promise}}]}}),angular.module("dias.ui.messages").constant("MAX_MSG",1),angular.module("dias.ui.messages").controller("MessagesController",["$scope","MAX_MSG",function(e,t){"use strict";e.alerts=[];var i=function(){document.exitFullscreen?document.exitFullscreen():document.msExitFullscreen?document.msExitFullscreen():document.mozCancelFullScreen?document.mozCancelFullScreen():document.webkitExitFullscreen&&document.webkitExitFullscreen()};window.$diasPostMessage=function(a,n){i(),e.$apply(function(){e.alerts.unshift({message:n,type:a||"info"}),e.alerts.length>t&&e.alerts.pop()})},e.close=function(t){e.alerts.splice(t,1)}}]),angular.module("dias.ui.messages").service("msg",function(){"use strict";var e=this;this.post=function(e,t){t=t||e,window.$diasPostMessage(e,t)},this.danger=function(t){e.post("danger",t)},this.warning=function(t){e.post("warning",t)},this.success=function(t){e.post("success",t)},this.info=function(t){e.post("info",t)},this.responseError=function(t){var i=t.data;if(i)if(i.message)e.danger(i.message);else if(401===t.status)e.danger("Please log in (again).");else if("string"==typeof i)e.danger(i);else for(var a in i)e.danger(i[a][0]);else e.danger("The server didn't respond, sorry.")}}),angular.module("dias.ui.utils").service("keyboard",function(){"use strict";var e={},t=function(e,t){for(var i=e.length-1;i>=0;i--)if(e[i].callback(t)===!1)return},i=function(i){var a=i.keyCode,n=String.fromCharCode(i.which||a).toLowerCase();e[a]&&t(e[a],i),e[n]&&t(e[n],i)};document.addEventListener("keydown",i),this.on=function(t,i,a){("string"==typeof t||t instanceof String)&&(t=t.toLowerCase()),a=a||0;var n={callback:i,priority:a};if(e[t]){var r,s=e[t];for(r=0;r<s.length&&!(s[r].priority>=a);r++);r===s.length-1?s.push(n):s.splice(r,0,n)}else e[t]=[n]},this.off=function(t,i){if(("string"==typeof t||t instanceof String)&&(t=t.toLowerCase()),e[t])for(var a=e[t],n=0;n<a.length;n++)if(a[n].callback===i){a.splice(n,1);break}}}),angular.module("dias.ui.utils").service("urlParams",function(){"use strict";var e={},t=location.pathname.split("/");t=t[t.length-1];var i=function(){var e=location.hash.replace("#","").split("&"),t={};return e.forEach(function(e){var i=e.match(/(.+)\=(.+)/);i&&3===i.length&&(t[i[1]]=decodeURIComponent(i[2]))}),t},a=function(e){var t="";for(var i in e)t+=i+"="+encodeURIComponent(e[i])+"&";return t.substring(0,t.length-1)},n=function(){var i=a(e);history.pushState(e,"",t+(i?"#"+i:""))},r=function(){var i=a(e);history.replaceState(e,"",t+(i?"#"+i:""))};this.pushState=function(e){t=e,n()},this.set=function(t){for(var i in t)e[i]=t[i];r()},this.unset=function(t){delete e[t],r()},this.get=function(t){return e[t]},e=history.state,e||(e=i())}),angular.module("dias.ui.utils").factory("debounce",["$timeout","$q",function(e,t){"use strict";var i={};return function(a,n,r){var s=t.defer();return function(){var o=this,u=arguments,d=function(){i[r]=void 0,s.resolve(a.apply(o,u)),s=t.defer()};return i[r]&&e.cancel(i[r]),i[r]=e(d,n),s.promise}()}}]),angular.module("dias.ui.utils").factory("filterExclude",function(){"use strict";var e=function(e,t){return e-t},t=function(t,i,a){a||(i=i.slice(0).sort(e));for(var n=t.slice(0).sort(e),r=0,s=0;r<i.length&&s<n.length;)i[r]<n[s]?r++:i[r]===n[s]?(t.splice(t.indexOf(n[s]),1),r++,s++):s++};return t}),angular.module("dias.ui.utils").factory("filterSubset",function(){"use strict";var e=function(e,t){return e-t},t=function(t,i,a){a||(i=i.slice(0).sort(e));for(var n=t.slice(0).sort(e),r=[],s=0,o=0;s<i.length&&o<n.length;)i[s]<n[o]?s++:i[s]===n[o]?(s++,o++):r.push(n[o++]);for(;o<n.length;)r.push(n[o++]);for(s=0;s<r.length;s++)t.splice(t.indexOf(r[s]),1)};return t});