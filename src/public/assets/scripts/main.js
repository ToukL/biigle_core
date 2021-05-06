(()=>{"use strict";var e,n={400:()=>{function e(e,n,t,i,s,o,r,a){var l,c="function"==typeof e?e.options:e;if(n&&(c.render=n,c.staticRenderFns=t,c._compiled=!0),i&&(c.functional=!0),o&&(c._scopeId="data-v-"+o),r?(l=function(e){(e=e||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext)||"undefined"==typeof __VUE_SSR_CONTEXT__||(e=__VUE_SSR_CONTEXT__),s&&s.call(this,e),e&&e._registeredComponents&&e._registeredComponents.add(r)},c._ssrRegister=l):s&&(l=a?function(){s.call(this,(c.functional?this.parent:this).$root.$options.shadowRoot)}:s),l)if(c.functional){c._injectStyles=l;var u=c.render;c.render=function(e,n){return l.call(n),u(e,n)}}else{var h=c.beforeCreate;c.beforeCreate=h?[].concat(h,l):[l]}return{exports:e,options:c}}var n=e({props:{entities:{type:Array,required:!0},filtering:{type:Boolean,default:!1},disabled:{type:Boolean,default:!1}},data:function(){return{filterQuery:""}},computed:{classObject:function(){return{"entity-chooser-list--disabled":this.disabled}}},methods:{select:function(e){this.disabled||this.$emit("select",e)}},watch:{filterQuery:function(e){this.$emit("filter",e)}}},(function(){var e=this,n=e.$createElement,t=e._self._c||n;return t("div",{staticClass:"entity-chooser-list",class:e.classObject},[e.filtering?t("input",{directives:[{name:"model",rawName:"v-model",value:e.filterQuery,expression:"filterQuery"}],staticClass:"form-control entity-chooser-list-search",attrs:{type:"text",placeholder:"Filter...",disabled:e.disabled},domProps:{value:e.filterQuery},on:{input:function(n){n.target.composing||(e.filterQuery=n.target.value)}}}):e._e(),e._v(" "),t("ul",e._l(e.entities,(function(n){return t("li",{key:n.id,on:{click:function(t){return e.select(n)}}},[n.icon?t("i",{class:"fa fa-"+n.icon}):e._e(),e._v(" "),t("span",{domProps:{textContent:e._s(n.name)}}),e._v(" "),t("span",[t("br"),t("span",{staticClass:"text-muted",domProps:{textContent:e._s(n.description)}})])])})),0)])}),[],!1,null,null,null);const t=e({components:{entityChooserList:n.exports},props:{entities:{type:Array,required:!0},disabled:{type:Boolean,default:!1}},data:function(){return{chosenIds:{},filterQuery:""}},computed:{unchosenEntities:function(){var e=this;return this.entities.filter((function(n){return!e.chosenIds[n.id]}))},unchosenFilteredEntities:function(){var e=this.filterQuery.trim();if(e){var n=e.toLowerCase().split(" ");return this.unchosenEntities.filter((function(e){var t=e.name.toLowerCase();return e.description&&(t+=" "+e.description.toLowerCase()),n.reduce((function(e,n){return e&&-1!==t.indexOf(n)}),!0)}))}return this.unchosenEntities},chosenEntities:function(){var e=this;return this.entities.filter((function(n){return e.chosenIds[n.id]}))},hasNoUnchosenEntities:function(){return 0===this.unchosenEntities.length},hasNoChosenEntities:function(){return 0===this.chosenEntities.length}},methods:{handleSelect:function(e){Vue.set(this.chosenIds,e.id,!0)},handleDeselect:function(e){this.chosenIds[e.id]=!1},chooseAll:function(){this.unchosenFilteredEntities.forEach(this.handleSelect)},chooseNone:function(){this.chosenEntities.forEach(this.handleDeselect)},handleFiltering:function(e){this.filterQuery=e}},watch:{chosenEntities:function(e){this.$emit("select",e)}}},(function(){var e=this,n=e.$createElement,t=e._self._c||n;return t("div",{staticClass:"entity-chooser"},[t("entity-chooser-list",{staticClass:"entity-chooser-list--left",attrs:{entities:e.unchosenFilteredEntities,filtering:!0,disabled:e.disabled},on:{select:e.handleSelect,filter:e.handleFiltering}}),e._v(" "),t("div",{staticClass:"entity-chooser-buttons"},[t("button",{staticClass:"btn btn-default btn-block",attrs:{disabled:e.disabled||e.hasNoUnchosenEntities,title:"Select all"},on:{click:e.chooseAll}},[e._v("all")]),e._v(" "),t("button",{staticClass:"btn btn-default btn-block",attrs:{disabled:e.disabled||e.hasNoChosenEntities,title:"Select none"},on:{click:e.chooseNone}},[e._v("none")])]),e._v(" "),t("entity-chooser-list",{staticClass:"entity-chooser-list--right",attrs:{entities:e.chosenEntities,disabled:e.disabled},on:{select:e.handleDeselect}})],1)}),[],!1,null,null,null).exports;var i=biigle.$require("messages").handleErrorResponse,s=biigle.$require("api.labelTree"),o=biigle.$require("core.mixins.loader"),r=biigle.$require("api.projects"),a=biigle.$require("core.components.typeahead"),l=biigle.$require("api.users"),c=biigle.$require("api.volumes"),u=biigle.$require("uiv.tabs"),h=biigle.$require("uiv.tab"),d={volumes:c,labelTrees:s,users:l};const f=e({mixins:[o],components:{tabs:u,tab:h,entityChooser:t},data:function(){return{exportApiUrl:null,allowedExports:[],entities:{volumes:[],labelTrees:[],users:[]},chosenEntities:{volumes:[],labelTrees:[],users:[]},currentTab:0,volumeIconMap:{}}},computed:{indexMap:function(){var e=this;return["volumes","labelTrees","users"].filter((function(n){return-1!==e.allowedExports.indexOf(n)}))},volumes:function(){var e=this;return this.entities.volumes.map((function(n){return n.description=n.projects.map((function(e){return e.name})).join(", "),n.icon=e.volumeIconMap[n.media_type_id],n}))},labelTrees:function(){return this.entities.labelTrees.map((function(e){return e.version&&(e.name=e.name+" @ "+e.version.name),e}))},users:function(){return this.entities.users.map((function(e){return e.name=e.firstname+" "+e.lastname,e.email&&(e.description=e.email),e}))},hasNoChosenVolumes:function(){return 0===this.chosenEntities.volumes.length},hasNoChosenLabelTrees:function(){return 0===this.chosenEntities.labelTrees.length},hasNoChosenUsers:function(){return 0===this.chosenEntities.users.length},volumeRequestUrl:function(){return this.exportApiUrl+"/volumes"+this.getQueryString("volumes")},labelTreeRequestUrl:function(){return this.exportApiUrl+"/label-trees"+this.getQueryString("labelTrees")},userRequestUrl:function(){return this.exportApiUrl+"/users"+this.getQueryString("users")}},methods:{handleSwitchedTab:function(e){this.currentTab=e},fetchEntities:function(e){var n=this;0===this.entities[e].length&&(this.startLoading(),d[e].get().then((function(t){return n.entities[e]=t.data}),i).finally(this.finishLoading))},handleChosenVolumes:function(e){this.chosenEntities.volumes=e},handleChosenLabelTrees:function(e){this.chosenEntities.labelTrees=e},handleChosenUsers:function(e){this.chosenEntities.users=e},getQueryString:function(e){var n=this.entities[e],t=this.chosenEntities[e];return n.length/2>t.length?"?only="+(t.map((function(e){return e.id})).join(",")||-1):n.length>t.length?"?except="+n.filter((function(e){return-1===t.indexOf(e)})).map((function(e){return e.id})).join(","):""}},watch:{currentTab:function(e){this.fetchEntities(this.indexMap[e])}},created:function(){this.exportApiUrl=biigle.$require("sync.exportApiUrl"),this.allowedExports=biigle.$require("sync.allowedExports");var e=biigle.$require("sync.mediaTypes");this.volumeIconMap[e.image]="image",this.volumeIconMap[e.video]="film",this.fetchEntities(this.indexMap[0])}},undefined,undefined,!1,null,null,null).exports,m=Vue.resource("api/v1/import{/token}");const p=e({mixins:[o],components:{entityChooser:t},data:function(){return{success:!1}},methods:{importSuccess:function(){this.success=!0}}},undefined,undefined,!1,null,null,null).exports;const b=e({data:function(){return{success:!1,userCandidates:[],conflictingParents:[],chosenLabels:[],importLabels:[]}},computed:{userMap:function(){var e={};return this.userCandidates.forEach((function(n){n.name=n.firstname+" "+n.lastname,e[n.id]=n})),e},labelMap:function(){var e={};return this.importLabels.forEach((function(n){e[n.id]=n})),e},conflictingParentMap:function(){var e={};return this.conflictingParents.forEach((function(n){e[n.id]=n})),e},conflictingLabels:function(){var e=this;return this.chosenLabels.filter((function(e){return e.hasOwnProperty("conflicting_name")||e.hasOwnProperty("conflicting_parent_id")})).map((function(n){return n.hasOwnProperty("conflicting_parent_id")&&(n.parent=e.labelMap[n.parent_id],n.conflicting_parent=e.conflictingParentMap[n.conflicting_parent_id]),n}))},hasConflictingLabels:function(){return this.conflictingLabels.length>0},hasUnresolvedConflicts:function(){var e=this;return!this.conflictingLabels.reduce((function(n,t){return n&&e.isLabelConflictResolved(t)}),!0)},nameConflictResolutions:function(){var e=this,n={};return this.conflictingLabels.forEach((function(t){e.hasLabelConflictingName(t)&&(n[t.id]=t.conflicting_name_resolution)})),n},parentConflictResolutions:function(){var e=this,n={};return this.conflictingLabels.forEach((function(t){e.hasLabelConflictingParent(t)&&(n[t.id]=t.conflicting_parent_resolution)})),n},panelClass:function(){return{"panel-danger":this.hasUnresolvedConflicts}},panelBodyClass:function(){return{"text-danger":this.hasUnresolvedConflicts}}},methods:{importSuccess:function(){this.success=!0},hasLabelConflictingName:function(e){return e.hasOwnProperty("conflicting_name")},hasLabelConflictingParent:function(e){return e.hasOwnProperty("conflicting_parent_id")},isLabelConflictResolved:function(e){return(!this.hasLabelConflictingName(e)||e.conflicting_name_resolution)&&(!this.hasLabelConflictingParent(e)||e.conflicting_parent_resolution)},chooseAllImportInformation:function(){var e=this;this.conflictingLabels.forEach((function(n){e.chooseImportParent(n),e.chooseImportName(n)}))},chooseAllExistingInformation:function(){var e=this;this.conflictingLabels.forEach((function(n){e.chooseExistingParent(n),e.chooseExistingName(n)}))},chooseImportParent:function(e){this.hasLabelConflictingParent(e)&&Vue.set(e,"conflicting_parent_resolution","import")},chooseImportName:function(e){this.hasLabelConflictingName(e)&&Vue.set(e,"conflicting_name_resolution","import")},chooseExistingParent:function(e){this.hasLabelConflictingParent(e)&&Vue.set(e,"conflicting_parent_resolution","existing")},chooseExistingName:function(e){this.hasLabelConflictingName(e)&&Vue.set(e,"conflicting_name_resolution","existing")}},created:function(){this.importLabels=biigle.$require("sync.importLabels")}},undefined,undefined,!1,null,null,null).exports;const g=e({mixins:[p,b],data:function(){return{importToken:null,adminRoleId:null,labelCandidates:[],conflictingParents:[],userCandidates:[],labelTreeCandidates:[],chosenLabelTrees:[],chosenLabels:[]}},computed:{chosenUsers:function(){var e=this,n=[];return this.chosenLabelTrees.forEach((function(t){t.members.forEach((function(t){t.role_id===e.adminRoleId&&-1===n.indexOf(t.id)&&n.push(t.id)}))})),n.filter((function(n){return e.userMap.hasOwnProperty(n)})).map((function(n){return e.userMap[n]}))},hasChosenUsers:function(){return this.chosenUsers.length>0},labels:function(){return this.labelCandidates.map((function(e){return e.description="Label tree: "+e.label_tree_name,e}))},hasNoChosenItems:function(){return 0===this.chosenLabelTrees.length&&0===this.chosenLabels.length},submitTitle:function(){return this.hasNoChosenItems?"Choose label trees or labels to import":this.hasUnresolvedConflicts?"Resolve the label conflicts":"Perform the import"},chosenLabelTreeIds:function(){return this.chosenLabelTrees.map((function(e){return e.id}))},chosenLabelIds:function(){return this.chosenLabels.map((function(e){return e.id}))}},methods:{handleChosenLabelTrees:function(e){this.chosenLabelTrees=e},handleChosenLabels:function(e){this.chosenLabels=e},performImport:function(){this.startLoading();var e={};this.chosenLabelTreeIds.length<this.labelTreeCandidates.length&&(e.only_label_trees=this.chosenLabelTreeIds),this.chosenLabelIds.length<this.labelCandidates.length&&(e.only_labels=this.chosenLabelIds),this.hasConflictingLabels&&(e.name_conflicts=this.nameConflictResolutions,e.parent_conflicts=this.parentConflictResolutions),m.update({token:this.importToken},e).then(this.importSuccess,i).finally(this.finishLoading)}},created:function(){this.importToken=biigle.$require("sync.importToken"),this.adminRoleId=biigle.$require("sync.adminRoleId"),this.labelCandidates=biigle.$require("sync.labelCandidates"),this.conflictingParents=biigle.$require("sync.conflictingParents"),this.userCandidates=biigle.$require("sync.userCandidates"),this.labelTreeCandidates=biigle.$require("sync.labelTreeCandidates").map((function(e){return e.version&&(e.name=e.name+" @ "+e.version.name),e}))}},undefined,undefined,!1,null,null,null).exports;const C=e({mixins:[p],data:function(){return{importToken:null,importCandidates:[],chosenCandidates:[]}},computed:{users:function(){return this.importCandidates.map((function(e){return e.name=e.firstname+" "+e.lastname,e.email&&(e.description=e.email),e}))},hasNoChosenUsers:function(){return 0===this.chosenCandidates.length},chosenCandidateIds:function(){return this.chosenCandidates.map((function(e){return e.id}))}},methods:{handleChosenUsers:function(e){this.chosenCandidates=e},performImport:function(){this.startLoading();var e={};this.chosenCandidates.length<this.importCandidates.length&&(e.only=this.chosenCandidateIds),m.update({token:this.importToken},e).then(this.importSuccess,i).finally(this.finishLoading)}},created:function(){this.importToken=biigle.$require("sync.importToken"),this.importCandidates=biigle.$require("sync.importCandidates")}},undefined,undefined,!1,null,null,null).exports;const v=e({mixins:[p,b],components:{typeahead:a},data:function(){return{importToken:null,adminRoleId:null,volumeCandidates:[],labelCandidates:[],conflictingParents:[],userCandidates:[],chosenVolumes:[],labelTreeCandidates:[],typeaheadTemplate:'<span v-text="item.name"></span><br><small v-text="item.description"></small>',availableProjects:[],targetProject:null}},computed:{volumes:function(){return this.volumeCandidates.map((function(e){return Vue.set(e,"new_url",e.url),"image"===e.media_type_name?e.icon="image":"video"===e.media_type_name&&(e.icon="film"),e}))},labelTreeMap:function(){var e={};return this.labelTreeCandidates.forEach((function(n){e[n.id]=n})),e},labelCandidateMap:function(){var e={};return this.labelCandidates.forEach((function(n){e[n.id]=n})),e},chosenUsers:function(){var e=this,n=[];return this.chosenVolumes.forEach((function(e){e.users.forEach((function(e){-1===n.indexOf(e)&&n.push(e)}))})),n.filter((function(n){return e.userMap.hasOwnProperty(n)})).map((function(n){return e.userMap[n]}))},hasChosenUsers:function(){return this.chosenUsers.length>0},chosenLabelTrees:function(){var e=this,n=[];return this.chosenVolumes.forEach((function(e){e.label_trees.forEach((function(e){-1===n.indexOf(e)&&n.push(e)}))})),n.filter((function(n){return e.labelTreeMap.hasOwnProperty(n)})).map((function(n){return e.labelTreeMap[n]}))},hasChosenLabelTrees:function(){return this.chosenLabelTrees.length>0},chosenLabelTreeAdmins:function(){var e=this,n=[];return this.chosenLabelTrees.forEach((function(t){t.members.forEach((function(t){t.role_id===e.adminRoleId&&-1===n.indexOf(t.id)&&n.push(t.id)}))})),n.filter((function(n){return e.userMap.hasOwnProperty(n)})).map((function(n){return e.userMap[n]}))},hasChosenLabelTreeAdmins:function(){return this.chosenLabelTreeAdmins.length>0},chosenLabelsOverride:function(){var e=this,n=[];return this.chosenVolumes.forEach((function(e){e.labels.forEach((function(e){-1===n.indexOf(e)&&n.push(e)}))})),n.filter((function(n){return e.labelCandidateMap.hasOwnProperty(n)})).map((function(n){return e.labelCandidateMap[n]}))},hasChosenLabels:function(){return this.chosenLabels.length>0},hasNoChosenItems:function(){return 0===this.chosenVolumes.length},submitTitle:function(){return this.hasNoChosenItems?"Choose volumes to import":this.hasUnresolvedConflicts?"Resolve the label conflicts":this.hasNoSelectedProject?"Select a target project":"Perform the import"},hasNoSelectedProject:function(){return null===this.targetProject},cantDoImport:function(){return this.loading||this.hasNoChosenItems||this.hasUnresolvedConflicts||this.hasNoSelectedProject},chosenVolumeIds:function(){return this.chosenVolumes.map((function(e){return e.id}))},newVolumeUrls:function(){var e={};return this.chosenVolumes.forEach((function(n){n.url!==n.new_url&&(e[n.id]=n.new_url)})),e},hasNewVolumeUrls:function(){return this.chosenVolumes.reduce((function(e,n){return e||n.url!==n.new_url}),!1)}},methods:{selectTargetProject:function(e){this.targetProject=e},handleChosenVolumes:function(e){this.chosenVolumes=e},performImport:function(){this.startLoading();var e={project_id:this.targetProject.id};this.chosenVolumeIds.length<this.volumes.length&&(e.only=this.chosenVolumeIds),this.hasNewVolumeUrls&&(e.new_urls=this.newVolumeUrls),this.hasConflictingLabels&&(e.name_conflicts=this.nameConflictResolutions,e.parent_conflicts=this.parentConflictResolutions),m.update({token:this.importToken},e).then(this.importSuccess,i).finally(this.finishLoading)}},watch:{hasNoChosenItems:function(e){var n=this;0!==this.availableProjects.length||e||r.query().then((function(e){n.availableProjects=e.body}),i)},chosenLabelsOverride:function(e){this.chosenLabels=e}},created:function(){this.importToken=biigle.$require("sync.importToken"),this.adminRoleId=biigle.$require("sync.adminRoleId"),this.volumeCandidates=biigle.$require("sync.volumeCandidates"),this.labelCandidates=biigle.$require("sync.labelCandidates"),this.conflictingParents=biigle.$require("sync.conflictingParents"),this.userCandidates=biigle.$require("sync.userCandidates"),this.labelTreeCandidates=biigle.$require("sync.labelTreeCandidates").map((function(e){return e.version&&(e.name=e.name+" @ "+e.version.name),e}))}},undefined,undefined,!1,null,null,null).exports;biigle.$mount("export-container",f),biigle.$mount("label-tree-import-container",g),biigle.$mount("user-import-container",C),biigle.$mount("volume-import-container",v)},608:()=>{}},t={};function i(e){var s=t[e];if(void 0!==s)return s.exports;var o=t[e]={exports:{}};return n[e](o,o.exports,i),o.exports}i.m=n,e=[],i.O=(n,t,s,o)=>{if(!t){var r=1/0;for(c=0;c<e.length;c++){for(var[t,s,o]=e[c],a=!0,l=0;l<t.length;l++)(!1&o||r>=o)&&Object.keys(i.O).every((e=>i.O[e](t[l])))?t.splice(l--,1):(a=!1,o<r&&(r=o));a&&(e.splice(c--,1),n=s())}return n}o=o||0;for(var c=e.length;c>0&&e[c-1][2]>o;c--)e[c]=e[c-1];e[c]=[t,s,o]},i.o=(e,n)=>Object.prototype.hasOwnProperty.call(e,n),(()=>{var e={355:0,392:0};i.O.j=n=>0===e[n];var n=(n,t)=>{var s,o,[r,a,l]=t,c=0;for(s in a)i.o(a,s)&&(i.m[s]=a[s]);for(l&&l(i),n&&n(t);c<r.length;c++)o=r[c],i.o(e,o)&&e[o]&&e[o][0](),e[r[c]]=0;i.O()},t=self.webpackChunkbiigle_sync=self.webpackChunkbiigle_sync||[];t.forEach(n.bind(null,0)),t.push=n.bind(null,t.push.bind(t))})(),i.O(void 0,[392],(()=>i(400)));var s=i.O(void 0,[392],(()=>i(608)));s=i.O(s)})();