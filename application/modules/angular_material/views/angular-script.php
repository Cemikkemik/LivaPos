<?php
global $Options;
$this->config->load( 'rest' );
?>

<script type="text/javascript">

    'use strict';

    var re;
    var regexIso8601 = re = /^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/;

    function convertDateStringsToDates(input) {
        // Ignore things that aren't objects.
        if (typeof input !== "object") return input;

        for (var key in input) {
            if (!input.hasOwnProperty(key)) continue;

            var value = input[key];
            var match;
            // Check for string properties which look like dates.
            // TODO: Improve this regex to better match ISO 8601 date strings.
            if (typeof value === "string" && (match = value.match(regexIso8601))) {
                // Assume that Date.parse can parse ISO 8601 strings, or has been shimmed in older browsers to do so.
                var milliseconds = Date.parse(match[0]);
                if (!isNaN(milliseconds)) {
                    input[key] = new Date(milliseconds);
                }
            } else if (typeof value === "object") {
                // Recurse into object
                convertDateStringsToDates(value);
            }
        }
    }

    tendooApp.config(function($compileProvider) {
        $compileProvider.preAssignBindingsEnabled(true);
    });

    tendooApp.config(["$httpProvider", function ($httpProvider) {
        $httpProvider.defaults.transformResponse.push(function(responseData){
            convertDateStringsToDates(responseData);
            return responseData;
        });
    }]);



    tendooApp.factory('$nutrition', ['$resource', function ($resource) {
      'use strict';

      return {
        entries : $resource( '<?php echo site_url( array( 'rest', 'angular', 'table', $table_name, ':id' ) );?>',{ id : '@_id' }, {
            get  : {
                method : 'GET',
                headers			:	{
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            },
            save    :   {
                method : 'POST',
                headers : {
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            },
            update :    {
                method : 'PUT',
                headers : {
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            },
            delete : {
                method : 'DELETE',
                headers : {
                    '<?php echo $this->config->item('rest_key_name');?>'	:	'<?php echo @$Options[ 'rest_key' ];?>'
                }
            }
        })
      };
    }]);

    tendooApp.directive('focusOn', function($timeout) {
      return {
        scope: { trigger: '=focusOn' },
        link: function(scope, element) {
          scope.$watch('trigger', function(value) {
            if(value === true) {
              //console.log('trigger',value);
              $timeout(function() {
                angular.element( element[0] ).focus();
                // scope.trigger = false;
            },100);
            }
          });
        }
      };
    });

    // To fix String to number on input
    tendooApp.directive('stringToNumber', function() {
      return {
        require: 'ngModel',
        link: function(scope, element, attrs, ngModel) {
          ngModel.$parsers.push(function(value) {
            return '' + value;
          });
          ngModel.$formatters.push(function(value) {
            return parseFloat(value, 10);
          });
        }
      };
    });

    tendooApp.controller('anguCrud', ['$nutrition', '$scope', '$timeout', '$mdDialog', '$location', function ($nutrition, $scope, $timeout, $mdDialog, $location ) {
        'use strict';



        /**
         *  Clear Search Terms
         *  @param
         *  @return
        **/

        $scope.clearSearchTerm      =   function( key ) {
            $scope.searchTerm[ key ]    =   '';
        }

        /**
        *  Close Search
        *  @param
        *  @return
        **/

        $scope.closeSearch            =   function() {
            $scope.filter.show        =   false;
            delete $scope.query.filter;
            $scope.getEntries();
        }

        /**
        *  Close Selected
        *  @param
        *  @return
        **/

        $scope.closeSelected          =   function(){
            $scope.selected           =   [];
        }

        /**
         *  Delete Selected
         *  @param
         *  @return
        **/

        $scope.deleteSelected       =   function( $event ){
            var confirm         =   $mdDialog.confirm().title( '<?php echo $AnguCrud->deleteSingleTitleLabel;?>' )
            .textContent( '<?php echo $AnguCrud->deleteBulkTextLabel;?>' )
            .targetEvent( $event )
            .ok( '<?php echo __( 'OK', 'angular_material' );?>' )
            .cancel( '<?php echo __( 'Cancel', 'angular_material' );?>' );

            $mdDialog.show( confirm ).then( function(){
                var ids         =   [];
                _.each( $scope.selected, function( value ){
                    ids.push(value.ID);
                });

                $nutrition.entries.delete({
                    'entry_id[]'              :   ids,
                    '__primaryCol'      : '<?php echo $AnguCrud->primaryCol;?>'
                },function(){
                    $scope.getEntries();
                });
            })

        }

        /**
         *  Edit Selected
         *  @param
         *  @return
        **/


        $scope.editSelected         =       function( $event ){
            document.location       =   '<?php echo $AnguCrud->baseUrl;?>/edit/' + $scope.selected[0].ID;
        }

        /**
         *  Export to Excel
         *  @param
         *  @return
        **/

        $scope.exportToExcel        =       function(){
            XLSX.writeFile({
                foo     :   'bar'
            }, 'out.xlsx');
        }

        function success(response) {
            $scope.entries          =   response.entries;
            $scope.totalEntries     =   response.total;
        }

        $scope.getEntries = function () {
            $scope.closeSelected();
            $scope.promise = $nutrition.entries.get($scope.query, success).$promise;
        };

        /**
         *  Go To selected
         *  @param
         *  @return
        **/

        $scope.goToSelected         =   function( url ) {
            var string              =   '';
            var i                   =   0;
            _.each( $scope.selected, function( value ){
                if( i == 0 ) {
                    string      +=  'selected[]=' + value.<?php echo $AnguCrud->getPrimaryCol();?>;
                    i++;
                } else {
                    string      +=  '&selected[]=' + value.<?php echo $AnguCrud->getPrimaryCol();?>;
                }
            });

            document.location   =   url + '?' + string;
        }

        /**
         *  turn for ngRepeat
         *  @param string relation key
         *  @return array
        **/

        $scope.turnForNgRepeat      =   function( value ) {
            var obj                 =   value;
            var obj_array           =   [];

            _.each( obj, function( value, key ) {
                obj_array.push({
                    key         :   key,
                    value       :   value
                });
            });

            return obj_array;
        }

        /**
         *  Load Entry
         *  @return void
        **/

        $scope.loadEntry            =   function( entry_id ){
            $scope.entryStatus      =   true;
            $nutrition.entries.get({ id : entry_id, __primaryCol : '<?php echo $AnguCrud->primaryCol;?>' },function( returned ) {
                $scope.fields           =   new Object;
                $scope.entryStatus      =   false;
                $scope.matching         =   <?php echo json_encode( $AnguCrud->getMatching() );?>;
                var matchinvert         =   _.invert( $scope.matching );
                _.each( returned.entries[0], function( value, key ) {
                    var index;
                    if( index   =   _.indexOf( _.values( $scope.matching ), key ) != -1 ) {
                        if( angular.isDefined( $scope.fieldsType[ matchinvert[ key ] ] ) ) {
                            if( $scope.fieldsType[ matchinvert[ key ] ] == 'select_relation_multiple' ) {
                                $scope.fields[ matchinvert[ key ] ]   =   JSON.parse("[" + value + "]");
                            } else {
                                $scope.fields[ matchinvert[ key ] ] = value;
                            }
                        } else {
                            $scope.fields[ matchinvert[ key ] ] = value;
                        }
                    } else {
                        $scope.fields[ key ]   =   value
                    }
                });
            })
        }

        /**
         *  Open Menu
         *  @param object mdOpenMenu
         *  @param object event
         *  @return
        **/

        $scope.openMenu = function($mdOpenMenu, ev) {
            $mdOpenMenu(ev);
            // $timeout( function(){
            //     angular.element( 'body' ).css( 'top', 'inherit' );
            //     angular.element( 'body' ).css( 'overflow', 'inherit' );
            //     angular.element( 'body' ).css( 'position', 'inherit' );
            //     angular.element( 'html' ).css( 'overflow-y', 'visible' );
            // }, 500 );
        };

        /**
         *  Sidebar Option Toggle
         *  @param null
         *  @return null
        **/

        $scope.sidebarOptionToggle      =   function( option, selected ){
            option.hide                 =   ! option.hide;
        }

        /**
         *  Submit Entry
         *  @param
         *  @return
        **/

        $scope.submitEntry          =   function( entriesForm ){

            if( entriesForm.$valid == false ) {
                angular.element( '[name="' + entriesForm.$name + '"]').find( 'input' ).each(function(){
                    $( this ).focus();
                    $( this ).blur();
                })
            }

            delete $scope.fields.__geniunes;
            delete $scope.fields.__relations;

            $scope.fields.__geniunes    =   _.keys( $scope.fields );
            $scope.fields.__relations   =   <?php echo json_encode( $AnguCrud->getRelations() );?>;

            $nutrition.entries.save( $scope.fields, function(){
                document.location           =   '<?php echo $AnguCrud->baseUrl . '?notice=entries_has_been_created';?>';
            },function(){
                alert( '<?php echo _s( 'An error occured', 'angular_material' );?>');
                // console.log( $scope.fields );
            })
        }

        /**
         *  Toggle Options
         *  @param
         *  @return
        **/

        $scope.toggleOptions        =   function(){
            if( ! $scope.optionsSidebarStatus ) {
                $scope.optionsSidebarStatus     =   true;
                $scope.mainContainerWidth       =   80;
                $scope.sidePanelWidth           =   20;
            } else {
                $scope.mainContainerWidth       =   100;
                $scope.sidePanelWidth           =   0;
                $scope.optionsSidebarStatus     =   false;
            }
        }

        /**
         *  Trigger Action
         *  @param string action
         *  @return
        **/

        $scope.triggerAction        =   function( action, entry_id, $event ){
            if( action == 'edit' ) {
                document.location       =   '<?php echo $AnguCrud->baseUrl;?>/edit/' + entry_id;
            } else if( action == 'delete' ) {

                var confirm         =   $mdDialog.confirm()
                .title( '<?php echo $AnguCrud->deleteSingleTitleLabel;?>' )
                .textContent( '<?php echo $AnguCrud->deleteSingleTextLabel;?>' )
                .targetEvent( $event )
                .ok( '<?php echo __( 'OK', 'angular_material' );?>' )
                .cancel( '<?php echo __( 'Cancel', 'angular_material' );?>' );

                $mdDialog.show( confirm ).then( function(){
                    $nutrition.entries.delete({
                        'id'                : entry_id,
                        '__primaryCol'      : '<?php echo $AnguCrud->primaryCol;?>'
                    }, function(){
                        $scope.getEntries();
                    })
                },function(){
                    return false;
                })
            }
        }

        /**
         *  Update Entry
         *  @param int entry id
         *  @return void
        **/

        $scope.updateEntry      =   function( id ) {

            if( entriesForm.$valid == false ) {
                angular.element( '[name="' + entriesForm.$name + '"]').find( 'input' ).each(function(){
                    $( this ).focus();
                    $( this ).blur();
                })
            }

            delete $scope.fields.__geniunes;
            delete $scope.fields.__priamryCol;
            delete $scope.fields.__relations;

            // Restore matching fields
            _.each( $scope.matching, function( value, key ) {
                $scope.fields[ value ]         =   $scope.fields[ key ];
                if( value != key ) {
                    delete $scope.fields[ key ];
                }
            });

            $scope.fields.__geniunes        =   _.keys( $scope.fields );
            $scope.fields.__primaryCol      =   '<?php echo $AnguCrud->primaryCol;?>';
            $scope.fields.__relations       =   <?php echo json_encode( $AnguCrud->getRelations() );?>;

            $nutrition.entries.update( { id : id },$scope.fields, function(){
                document.location           =   '<?php echo $AnguCrud->baseUrl . '?notice=entries_has_been_updated';?>';
            },function(){
                alert( '<?php echo _s( 'An error occured', 'angular_material' );?>');
                console.log( $scope.fields );
            })
        }

        // Vars
        $scope.crudHeight               =   angular.element( '.content-wrapper' ).height();
        $scope.optionsSidebarStatus     =   false;

        $scope.exportFields             =   <?php echo json_encode( $AnguCrud->getColumns() );?>;
        delete $scope.exportFields.$AnguCrudActions;

        $scope.selected                 =   new Array;
        $scope.selectOptions            =   <?php echo json_encode( ( array ) $AnguCrud->getSelectOptions() );?>;
        $scope.filter                   =   {
            show        :   false
        };
        $scope.showAddNew               =   false;
        $scope.showEdit                 =   false;
        $scope.menuActions              =   {
            edit        :   { label : '<?php echo _s( 'Edit', 'angular_material' );?>', icon : 'fa fa-edit'},
            delete      :   { label : '<?php echo _s( 'Delete', 'angular_material' );?>', icon : 'fa fa-trash' }
        }
        $scope.matching                 =   <?php echo json_encode( $AnguCrud->getMatching() );?>;
        $scope.relations                =   <?php echo json_encode( ( Array ) $relations );?>;
        $scope.mainContainerWidth       =   100;
        $scope.sidePanelWidth           =   0;
        $scope.relationsObject          =   {};
        $scope.fieldsType               =   <?php echo json_encode( $AnguCrud->getFieldType() );?>;

        _.each( $scope.relations, function( value, key ) {
            $scope.relationsObject[ key ]     =   $scope.turnForNgRepeat( value );
        })

        // Remove Title
        angular.element( '.content-header' ).remove();

        // Stop Keydown bind on select field
        angular.element( '.select-field' ).on('keydown', function(ev) {
            ev.stopPropagation();
        });

        $scope.query = {
            order   : '<?php echo $AnguCrud->primaryCol;?>',
            limit   : <?php echo $AnguCrud->entriesPerPage;?>,
            page    : 1,
            __primaryCol    :   '<?php echo $AnguCrud->primaryCol;?>',
            'columns[]' : <?php echo json_encode( array_keys( $AnguCrud->getColumns() ) );?>,
            relations   :   '<?php echo json_encode( $AnguCrud->getRelations() );?>'
        };

        $scope.$watch( 'query.filter', function(){
            if( angular.isDefined( $scope.query.filter ) ) {
                $scope.getEntries();
            }
        });

        $scope.hideColumn       =   new Object;
        <?php foreach( ( Array ) $AnguCrud->getColumns() as $key => $title ):?>
        $scope.hideColumn[ '<?php echo $key;?>' ]       =   {
            hide        :   false,
            title       :   '<?php echo addslashes( $title );?>'
        }
        <?php endforeach;?>
    }]);
</script>
