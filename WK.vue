<template>

  <div id="WK">
    
<div id="full_links">
        <div id="links" v-if="ok">
         <p class="headline">German and Austrian casualty lists of the First World War - Saxony, Poland, Czechia</p>
           <b-button v-on:click="listCategories()">Herkunftsorte</b-button>
         
 

   </div>
   
<div id="detail"/> 
  


     
     
      
     <div id="liste" v-if="liste_toggle">
       {{zahlMarker}} Ergebnisse
     <b-list-group>
     <b-list-group-item v-for="item in kategorie_ar" :key="item.index" @click="getCategories(item)">{{item }} </b-list-group-item>
  </b-list-group>

    </div>
       </div>
    <div id="full_div">

    
  
         

    <l-map
      :zoom="zoom"
      :center="center"
      :bounds="bounds"
      style="height: 1000px"
      @update:center="centerUpdate"
      @update:zoom="zoomUpdate"
      @popupclose="beschreibung_toggle=false"
      
    >
      <l-tile-layer
        :url="url"
        :attribution="attribution"
      />

     <v-marker-cluster>
       <l-marker
     
            v-for="marker in markers"
            :key="marker.index"
                       :lat-lng="marker.position" 
                      :icon="marker.icon2"
                    :icon-size="marker.iconSize"
            @click="alert(marker.index)"
      >
    <l-popup>
       
        <h5>{{titel}}</h5>
        
     <p v-html="ort" v-linkified />
  

      <div v-html="beschreibung" v-linkified />
 
  
    
    </l-popup>
     </l-marker>
       </v-marker-cluster>
    </l-map>
</div>
<div>Datenquelle: <a href="https://codingdavinci.de/daten/german-and-austrian-casualty-lists-first-world-war-saxony-poland-czechia" target="_blank">Coding da Vinci Ost 2022</a></div>
    </div>


  
</template>

<script>
import {icon,latLng,latLngBounds } from "leaflet";
import { LMap, LTileLayer, LMarker, LPopup, LTooltip, LGeoJson } from "vue2-leaflet";
import Vue2LeafletMarkercluster from 'vue2-leaflet-markercluster'
import axios from "axios";

export default {
  name: "LoadTest",
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LPopup,
   
    LGeoJson,
    'v-marker-cluster': Vue2LeafletMarkercluster

  },
  
  data() {
    return {
      
      zoom: 14,
      center: latLng(51.5440, 7.2560),
      url: "https://{s}.tile.osm.org/{z}/{x}/{y}.png",
      attribution:
        '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors',
      currentZoom: 14,
      currentCenter: latLng(51.5440, 7.2560),
       defaultIcon: icon({
        iconUrl: 'marker.png',
       
        iconSize:     [64,64],
        
      }),
             layers:[],
      geojson: null,
beschreibung_toggle:false,
liste_toggle:true,
bilder_toggle:false,


     counter:0,
      titel:null,
       autor:null,
       kategorie:"5x5.000",
       category:"all",
       link:null,
      
       typ:null,
       markers: [],
        markers2: [],
       zahlMarker:null,
       zeit:null,
       ort:null,
       bilder:null,
       bildunterschrift:null,
       searchText: 'e',
     
      titel_ar:[],
      kategorie_ar:[],
      ok:false,
       bounds: latLngBounds([
        [40.70081290280357, -74.26963806152345],
        [40.82991732677597, -74.08716201782228]
      ]),
       selected: null,
        options: [
          { value: null, text: 'Bitte wählen Sie eine Kategorie:' },
          { value: 'Ersatz-Bataillon, Reserve-Infanterie-Regiment 102', text: 'Ersatz-Bataillon, Reserve-Infanterie-Regiment 102' },
          { value: 'Infanterie-Regiment 177', text: 'Infanterie-Regiment 177' }
        ]
    };
  },
 
computed : {
   dynamicSize() {
        return [50-(this.currentZoom), 50-(this.currentZoom)];
    },
      itemsSearched : {
        get: function(){

        var self = this;
         var Titelar=[];
        var Titelar2=[];
        var index=0;
        var index2=0;
      
        if(self.searchText!=="")
        {
       

       var Singlemarkerar=[];
        
        this.layers.forEach((layer) => {
  
           var icon3=this.getIcon(layer.kategorie);
          
            var singlemarker = {
        position: { lat: layer.latitude, lng: layer.longitude },
        index: layer.nummer,
        icon2: icon3,
      };
 
 Singlemarkerar.push(singlemarker);
          
         Titelar.push(layer.titel);
     
         index++;
    
        });//this layers

           Titelar.filter(function(item){
            if(item.indexOf(self.searchText) >= 0)
            {
      var titelobjekt={titel:item, index:index2};
              Titelar2.push(titelobjekt);
                      index2++;
            }
          //return item.indexOf(self.searchText) >= 0;
        });
       
        return Titelar2;
        }
        else
        {
           
        
Titelar2=this.titel_ar;
          return Titelar2;
        }
      },//get
  
      set: function (test) {

       var index2=0;
        this.layers.forEach((layer) => {
   var singlemarker=[layer.latitude, layer.longitude, index2];

 if(test[index2].titel==layer.titel)
 {
this.markers.push(singlemarker);


 }
 index2++;
        });
       

      
      }//set
      }
  },
  methods: {

     changeMarkersSearch()

    {
      this.ok=true;
         // this.kategorie=item;
          this.beschreibung_toggle=false;
        this.liste_toggle=true;
      
this.markers = [];
 this.markers2 = [];
  var index=0;
  var index2=0;
this.layers.forEach((layer) => {
   var position={ lat: layer.latitude, lng: layer.longitude };
   var icon3=this.getIcon(layer.kategorie);
   var singlemarker = {
        position: { lat: layer.latitude, lng: layer.longitude },
        index: layer.nummer,
        icon2: icon3,
      };
 
this.itemsSearched.forEach((item)=> {

if(item.titel==layer.titel)
{
this.markers.push(singlemarker);
this.markers2.push(position);
index2++;
}
});

index++;

  });
 
this.zahlMarker=index2;
this.bounds=latLngBounds(this.markers2);

    },
     changeMarkersZeit(item)

    {
      this.ok=true;
       this.beschreibung_toggle=false;
        this.liste_toggle=true;
            
      this.zeit=item;
          //this.kategorie="";
          //this.titel="";
          //this.beschreibung="";
          // this.autor="";
this.markers = [];
this.markers2 = [];
 this.titel_ar=[];
  this.searchText="";
  var index=0;
  var index2=0;
  this.layers.forEach((layer) => {
    var position={ lat: layer.latitude, lng: layer.longitude };
    var icon3=this.getIcon(layer.kategorie);
var singlemarker = {
        position: { lat: layer.latitude, lng: layer.longitude },
        index: index,
        icon2: icon3,
      };

 if(this.kategorie==layer.kategorie && item==layer.zeit) 
 {
this.markers.push(singlemarker);
this.markers2.push(position);

var titelobjekt={titel:layer.titel, index:index2};
this.titel_ar.push(titelobjekt);
index2++;
 }
else if(item==layer.zeit && this.kategorie=="Alle")
{
 
this.markers.push(singlemarker);
this.markers2.push(position);
 titelobjekt={titel:layer.titel, index:index2};
this.titel_ar.push(titelobjekt);
 index2++;
}
   
   index++;

  });
 
this.zahlMarker=index2;
this.bounds=latLngBounds(this.markers2);


    },
    weg()
    {
this.ok=false;
    },
    mehr()
    {
this.beschreibung_toggle=true;
    },
     weniger()
    {
this.beschreibung_toggle=false;
    },
      alert(item) {
      // this.kategorie="Autor";
        //this.beschreibung=item[2];
        this.counter++;
        if(this.counter==this.zahlMarker)
        {
          this.counter=0;
        }
this.ok=true;
        var i=0;
        this.layers.forEach((layer) => {
   if(layer.nummer==item)
   {
     this.titel=layer.titel;
    this.beschreibung=layer.beschreibung;
   
    this.kategorie=layer.kategorie;
    this.ort=layer.ort;
    this.link=layer.link;
   
    
    //this.beschreibung_toggle=true;
    //this.liste_toggle=false;
   }
   
   i++;

  });
    
    },
     alert3(item) {
      // this.kategorie="Autor";
        //this.beschreibung=item[2];
         this.markers=[];
          this.markers2=[];
        this.counter++;
        if(this.counter==this.zahlMarker)
        {
          this.counter=0;
        }
this.ok=true;
        var i=0;
        this.layers.forEach((layer) => {
   if(i==item)
   {
     this.titel=layer.titel;
    this.beschreibung=layer.beschreibung;
       this.kategorie=layer.kategorie;
    this.ort=layer.ort;
    this.link=layer.link;
  
    this.beschreibung_toggle=true;
    this.liste_toggle=false;
    
var position={ lat: layer.latitude, lng: layer.longitude };
var icon3=this.getIcon(layer.kategorie);
   var singlemarker = {
        position: { lat: layer.latitude, lng: layer.longitude },
        index: i,
        icon2: icon3,
      };
this.markers.push(singlemarker);
this.markers2.push(position);
   }
   
   i++;

  });
    this.bounds=latLngBounds(this.markers2);
    },
   listCategories()
    {
        this.layers.forEach((layer) => {
           this.kategorie=layer.kategorie;
            if(layer.kategorie!=="")
    {
      if(this.kategorie_ar.includes(layer.kategorie))
      {

      }
      else
      {
           this.kategorie_ar.push(this.kategorie);
      }
    }
           
         });
    },
    getCategories(category)
    {
     
     this.category=category;
         this.markers=[];
          this.markers2=[];
          this.titel_ar=[];
           this.liste_toggle=true;
 this.beschreibung_toggle=false;
 this.bilder_toggle=false;
       this.zahlMarker=0;
this.ok=true;
        var i=0;
 
        this.layers.forEach((layer) => {
         
   if(category == layer.kategorie)
   {
     this.zahlMarker++;
     this.titel=layer.titel;
    this.beschreibung=layer.beschreibung;
  
    
    this.ort=layer.ort;
    this.link=layer.link;
    
    this.zahl=layer.zahl;
  
    this.typ=layer.typ;
   
    
var position={ lat: layer.latitude, lng: layer.longitude };
var icon3=this.getIcon(layer.kategorie);
   var singlemarker = {
        position: { lat: layer.latitude, lng: layer.longitude },
        index: layer.nummer,
        icon2: icon3,
      };
this.markers.push(singlemarker);
this.markers2.push(position);
var titelobjekt={titel:layer.titel, index:layer.nummer};
this.titel_ar.push(titelobjekt);
   }
   
   i++;

  });
    this.bounds=latLngBounds(this.markers2);

    },
    toggle()
    {

  
         this.markers=[];
          this.markers2=[];
          this.titel_ar=[];
           this.liste_toggle=true;
 this.beschreibung_toggle=false;
 this.bilder_toggle=false;
       this.zahlMarker=0;
this.ok=true;
     
 
        this.layers.forEach((layer) => {
         
 
     this.zahlMarker++;
     this.titel=layer.titel;
    this.beschreibung=layer.beschreibung;
    
    this.kategorie=layer.kategorie;
    this.ort=layer.ort;
    this.link=layer.link;
    
   
   
  
    
var position={ lat: layer.latitude, lng: layer.longitude };
var icon3=this.getIcon(layer.kategorie);
   var singlemarker = {
        position: { lat: layer.latitude, lng: layer.longitude },
        index: layer.nummer,
        icon2: icon3,
      };
this.markers.push(singlemarker);
this.markers2.push(position);
var titelobjekt={titel:layer.titel, index:layer.nummer};
this.titel_ar.push(titelobjekt);
   
   


  });
    this.bounds=latLngBounds(this.markers2);


    },
         alert2(item) {
           this.ok=true;
          this.markers=[];
          this.markers2=[];
       //this.kategorie="Autor";
        //this.beschreibung=item[2];
      var index=0;
        this.layers.forEach((layer) => {
   if(layer.titel==item)
   {
     this.counter=index;
     this.beschreibung_toggle=true;
     var position={ lat: layer.latitude, lng: layer.longitude };
     var icon3=this.getIcon(layer.kategorie);
   var singlemarker = {
        position: { lat: layer.latitude, lng: layer.longitude },
        index: layer.nummer,
        icon2: icon3,
      };
      this.markers.push(singlemarker);
      this.markers2.push(position);
    this.titel=layer.titel;
    this.beschreibung=layer.beschreibung;
    
    this.kategorie=layer.kategorie;
    this.ort=layer.ort;
    this.link=layer.link;
   
 
    this.kategorie=layer.kategorie;
     //this.liste_toggle=false;
     this.beschreibung_toggle=false;
   
   }

   
  index++;

  });
       this.zahlMarker=1;
this.bounds=latLngBounds(this.markers2);
    },
    zoomUpdate(zoom) {
      this.currentZoom = zoom;
    },
   

greet: function (event) {
  if (event) {
        //KKK
      }
       
     
 },
 getIcon(ic)
{
  var icon=null;

return icon;
},
    centerUpdate(center) {
      this.currentCenter = center;
    }

 
  

  },
   

   async created() {
    this.loading = true;
    this.ok=true;
    const response1 = await fetch(".../map.geojson")
    const data1 = await response1.json();
    this.geojson = data1;
    axios
      .get(
        ""
      )
      .then(response => {
        this.layers = response.data;
        this.loading = false;
          var index=0;
          
  this.layers.forEach((layer) => {
    var icon3=this.getIcon(layer.kategorie);
var position={ lat: layer.latitude, lng: layer.longitude };
    var singlemarker = {
        position: { lat: layer.latitude, lng: layer.longitude },
        index: layer.nummer,
        icon2: icon3,
      };
 

  this.markers2.push(position);
this.markers.push(singlemarker);
var titelobjekt={titel:layer.titel, index:layer.nummer};
this.titel_ar.push(titelobjekt);
 
   index++;

  });
this.zahlMarker=index;
this.zeit="Alle";
this.kategorie="Alle";

this.bounds=latLngBounds(this.markers2);
      })
        .then(function(){
 //this.markers = [];
 


        });
  }
};
</script>
<style>
@import '../../node_modules/leaflet/dist/leaflet.css';
 @import "../../node_modules/leaflet.markercluster/dist/MarkerCluster.css";
  @import "../../node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css";
.leaflet-fake-icon-image-2x {
  background-image: url(../../node_modules/leaflet/dist/images/marker-icon-2x.png);
}
.leaflet-fake-icon-shadow {
  background-image: url(../../node_modules/leaflet/dist/images/marker-shadow.png);
}
.leaflet-container {

font-size: 1em;
}

body {
  margin: 0px;
  font-family: Helvetica, Verdana, sans-serif;
  
}
 
      
#side {
  float: left;
 
}
#weg
{
visibility: hidden;

}
#nav3
{
visibility: hidden;

}
#full_div {

width:70%;
  top: 0;
  right: 0;
float:left;
  bottom: 0;
  padding-left: 8px;
  border-left: 1px solid #ccc;
   z-index: 0;
}
#full_links {
  float:left;
  overflow: hidden;
  width: 30%;
  bottom: 0;
  padding-left: 8px;
  border-left: 1px solid #ccc;
   z-index: 0;
}

#liste
{
height:700px;
overflow: scroll;

}
.headline{
color:#69ba73;
font-size:2em;
font-weight:bold;


}
ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
}

li {
  font: 200 15px/1.5 Helvetica, Verdana, sans-serif;
  border-bottom: 1px solid #ccc;
}

li:last-child {
  border: none;
}

li a {
  font-size: 15px;
  padding-left: 8px;
  text-decoration: none;
  color: #000;
  display: block;

  -webkit-transition: font-size 0.3s ease, background-color 0.3s ease;
  -moz-transition: font-size 0.3s ease, background-color 0.3s ease;
  -o-transition: font-size 0.3s ease, background-color 0.3s ease;
  -ms-transition: font-size 0.3s ease, background-color 0.3s ease;
  transition: font-size 0.3s ease, background-color 0.3s ease;
}

li a:hover {
  font-size: 20px;
  background: #f6f6f6;
}
.logo
{
width:150px;

}
#unten
{

  visibility: hidden;
}
@media (max-width: 800px){
.headline{
font-size: 14px;
}
#full_links{
 width: 100%;

  padding-left: 8px;
  position: relative;
   z-index: 5;

   background-color: #f6f6f6;
   height:50%;
}

#full_div
{

position: absolute;
top: 0;

width:100%;

  padding-left: 8px;
  border-left: 1px solid #ccc;
   z-index: 0;
}
#liste
{
height:300px;
overflow: scroll;

}
}
@media  (min-width:1000px) {
#full_links{
 width: 30%;

  padding-left: 8px;
  position: relative;
   z-index: 5;

   background-color: #f6f6f6;
}

#full_div
{
float:left;
top:0;

width:70%;

  padding-left: 8px;
  border-left: 1px solid #ccc;
   z-index: 0;
}
#liste
{
height:800px;
overflow: scroll;

}

#head
{
  position:relative;
  float:right;
  right:120px;
  z-index:10;
  padding:5px;
  width:8%;
 
}
.logo
{
width:80%;

}
.search-bar
{
  width:42%;
}

#weg
{
visibility: visible;

}

#weiter
{

  float:right;
}

}
</style>
