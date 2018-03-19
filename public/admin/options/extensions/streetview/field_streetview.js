/*
 Streetview field
 */

/*global jQuery, document, redux_change, redux*/

(function( $ ) {
    'use strict';
    


    $(function(){
        
        google.maps.event.addListener( $.pfcormarker, 'dragend', function ( event )
        {
          $.pfstmapdestroy();
          $.pfstmapregenerate();
          $.panoramamap.setCenter(event.latLng);
          $.pfpanorama.setPosition(event.latLng);
        } );


        $.pfstmapregenerate = function(){
          console.log('STW Generate Run');
          var current_heading = parseFloat($("#webbupointfinder_item_streetview-heading").val());
          var current_pitch = parseFloat($("#webbupointfinder_item_streetview-pitch").val());
          var current_zoom = parseInt($("#webbupointfinder_item_streetview-zoom").val());

          var pfitemcoordinatesLat = parseFloat($("#pfitempagestreetviewMap").data('pfcoordinateslat'));
          var pfitemcoordinatesLng = parseFloat($("#pfitempagestreetviewMap").data('pfcoordinateslng'));
          var pfitemzoom = parseInt($("#pfitempagestreetviewMap").data('pfzoom'));
          var testcoors = [pfitemcoordinatesLat,pfitemcoordinatesLng];
          var pfitemcoordinates_output = new google.maps.LatLng(testcoors[0],testcoors[1]);

          var defaultLoc;
          defaultLoc = $('.rwmb-map-canvas').data( 'default-loc');

          var defaultloc = defaultLoc ? defaultLoc.split( ',' ) : [40.71275, -74.00597];
          var defaultlocl = new google.maps.LatLng( defaultloc[0], defaultloc[1] );

          var curlatLng = (pfitemcoordinatesLat)? pfitemcoordinates_output:defaultlocl;

          if (current_heading != 0 && current_pitch != 0) {
            $('.pfitempagestreetview').remove();
            
            $("#pfitempagestreetviewMap").gmap3({
              map:{
                options:{
                  zoom: pfitemzoom, 
                  mapTypeId: google.maps.MapTypeId.ROADMAP, 
                  streetViewControl: true, 
                  center: pfitemcoordinates_output 
                },
                callback:function(map){
                  $.panoramamap = map;
                }
              },    
              streetviewpanorama:{
                options:{
                  id: "streetviewpanorama",
                  name: "streetviewpanorama",
                  container: $(document.createElement("div")).addClass("pfitempagestreetview").insertAfter($("#pfitempagestreetviewMap")),
                  opts:{
                    position: pfitemcoordinates_output,
                    pov: {
                      heading: current_heading,
                      pitch: current_pitch,
                      zoom: current_zoom
                    }
                  }
                }
              }
            });

        }else{
          $("#pfitempagestreetviewMap").gmap3({
            map:{
              options:{
                zoom: pfitemzoom, 
                mapTypeId: google.maps.MapTypeId.ROADMAP, 
                streetViewControl: true, 
                center: curlatLng 
              },
              callback:function(map){
                $.panoramamap = map;
              }
            },    
            streetviewpanorama:{
              options:{
                id: "streetviewpanorama",
                container: $(document.createElement("div")).addClass("pfitempagestreetview").insertAfter($("#pfitempagestreetviewMap")),
                opts:{
                  position: curlatLng,
                  pov: {
                    heading: 90,
                    pitch: 5,
                    zoom: 1
                  }
                }
              }
            }
          });
        };
        }

        $.pfstmapdestroy = function(){
          $('#pfitempagestreetviewMap').gmap3('destroy').html('<div id="pfitempagestreetviewMap"></div>');
          $('.pfitempagestreetview').remove();
        }


        setTimeout(function(){
          $.pfstmapregenerate();
        }, 3000);


        setTimeout(function(){
          var panorama = $("#pfitempagestreetviewMap").gmap3({get: {name: "streetviewpanorama"}});
                    
          var current_heading = parseFloat($("#webbupointfinder_item_streetview-heading").val());
          var current_pitch = parseFloat($("#webbupointfinder_item_streetview-pitch").val());
          var current_zoom = parseInt($("#webbupointfinder_item_streetview-zoom").val());
          
          $.pfpanorama = panorama;
          $.pfmapHeading = panorama.getPov().heading;
          $.pfmapPitch = panorama.getPov().pitch;
          $.pfmapZoom = panorama.getPov().zoom;

           if (current_heading != 0 && current_pitch != 0) {
             setTimeout(function(){
                $.pfpanorama.addListener('pov_changed', function() {
                  $("#webbupointfinder_item_streetview-heading").val($.pfpanorama.getPov().heading);
                  $("#webbupointfinder_item_streetview-pitch").val($.pfpanorama.getPov().pitch)
                  $("#webbupointfinder_item_streetview-zoom").val($.pfpanorama.getPov().zoom)
                });

              }, 10000);
           }else{
             setTimeout(function(){
                $.pfpanorama.addListener('pov_changed', function() {
                  $("#webbupointfinder_item_streetview-heading").val($.pfpanorama.getPov().heading);
                  $("#webbupointfinder_item_streetview-pitch").val($.pfpanorama.getPov().pitch)
                  $("#webbupointfinder_item_streetview-zoom").val($.pfpanorama.getPov().zoom)
                });

              }, 7000);
           }

        }, 5000);

     })

    
    
})( jQuery );
