    var resSlide;
    var list;
    var list2;

    $(document).ready(function () {
        disable_but(true);

        $("#range_1").ionRangeSlider({
            min: 1,
            max: 50,
            from: 1,
            to: 50,
            type: 'double',
            step: 1,
            maxPostfix: "+",
            prettify: true,
            hasGrid: true,
            onChange: function (obj) {
                list.filter(function (item) {
                    var min = obj["fromNumber"];
                    var max = obj["toNumber"];
                    if (item.values().val >= min && item.values().val <= max) {
                        return true;
                    } else {
                        return false;
                    }

                });
            }
        });

        var options = {
            valueNames: ['nam', 'val'],
            page: 1000
        };

        list = new List('players', options);
        
        var options2 = {
            valueNames: ['user_name'],
            page: 1000
        };

        list2 = new List('users', options2);

        
        
        



        function my_fun() {
            $("#range_1").ionRangeSlider("update", {
                min: 1, // change min value
                max: 50, // change max value
                from: 1, // change default FROM setting
                to: 50, // change default TO setting
            });
            document.getElementById("search-element").value = "";
            list.filter();
            list.search();
        }
        resSlide = my_fun;




    });

    function resetSlide() {
        resSlide();

    }

    var count_check = function(){
        var number=document.querySelectorAll('input[type="checkbox"]:checked').length;
        document.getElementById("number_users_selected").innerHTML=number;
    };
    
    var select_all_teams = function(bo){

        var items=document.querySelectorAll('.select_teams');

        
        
        for(var i=0;i<items.length;i++){
            $(items[i]).prop("checked",true);  
        }
        count_check();
    };

    var deselect_all_teams = function(bo){
        
        var items=document.querySelectorAll('.select_teams');
        
        
        for(var i=0;i<items.length;i++){
            $(items[i]).prop("checked",false);  
        }
        count_check();
    };


    var disable_but = function (bo) {
        var mod_button = document.getElementById("mod-button");
        if(mod_button!=null){
            mod_button.disabled = bo;
            if (bo) {
                mod_button.className = "disabled_but";
            } else {
                mod_button.className = "";
            }
        }
    };

    var change = function (obj) {
        var min = obj["fromNumber"];
        var max = obj["toNumber"];


        var players = document.getElementsByClassName("new-player");
        var dis = "none";

        var element = document.getElementsByClassName('but-over');
        var role_select;
        for (i = 0; i < element.length; ++i) {
            if (element[i].style.display == "none") {

            } else {
                role = element[i].parentNode.getElementsByClassName("but-role")[0].innerHTML;
            }

        }


        for (var i = 0; i < players.length; ++i) {
            var player = players[i];
            var value = player.getAttribute("data-value");
            if (value < min || value > max) {
                player.style.display = "none";

            } else {
                if (role.toLowerCase() == player.getAttribute("role").toLowerCase()) {
                    player.style.display = "inline";
                } else {
                    player.style.display = "none";
                }

            }


        }

    };

    var hover = function (obj) {
        var element = obj.getElementsByClassName('but-over');
        element[0].style.display = "block";
    }

    var stophover = function (obj) {
        var element = obj.getElementsByClassName('but-over');
        element[0].style.display = "none";
    }

    var select_role_create = function (role, obj) {
        
        resetSlide();

        var element = document.getElementsByClassName('but-over');
        for (i = 0; i < element.length; i++) {
            element[i].style.display = "none";
            element[i].parentNode.setAttribute("onmouseout", "stophover(this)");
        }

        element = obj.getElementsByClassName('but-over');
        obj.removeAttribute("onmouseout");
        element[0].style.display = "block";


        var free_table = document.getElementById("free-table");
        
        var arr = free_table.getElementsByClassName("new-player");
        for (i = 0; i < arr.length; i++) {
            var element = arr[i];
            if (element.getAttribute("role").toLowerCase() != role.toLowerCase()) {
                element.style.display = "none";

            } else {
                if (element.getAttribute("in-roster") == "yes") {
                    element.style.display = "none";
                } else {
                    element.style.display = "block";
                }
            }
        };


        

    }

    var select_role_market = function (role) {

        list.filter();
        list.search();


        /*var free_table = document.getElementById("free-table");
        var arr = free_table.getElementsByClassName("new-player");
        console.log(arr);
        for (i = 0; i < arr.length; i++) {
            var element = arr[i];
            if (element.getAttribute("role").toLowerCase() != role.toLowerCase()) {
                element.style.display = "none";

            } else {
                if (element.getAttribute("in-roster") == "yes") {
                    element.style.display = "none";
                } else {
                    element.style.display = "block";
                }
            }
        };*/


        resetSlide();

    }

    var balance = function () {
        var item = document.getElementById("balance-display");
        return parseInt(item.innerHTML);
    };

    var cost_change_create = function (cost, add) {
        if (add) {
            var item = document.getElementById("balance-display");
            var balance = item.innerHTML;
            item.innerHTML = parseInt(balance) - parseInt(cost);
            var value = parseInt(item.innerHTML);
            if (value < 0) disable_but(true);
            else disable_but(false);

        } else {
            var item = document.getElementById("balance-display");
            var balance = item.innerHTML;
            item.innerHTML = parseInt(balance) + parseInt(cost);
            var value = parseInt(item.innerHTML);
            if (value < 0) disable_but(true);
            else disable_but(false);

        }
    };

    var cost_change_market = function () {
        if (ol !== null && ne !== null) {
            var item = document.getElementById("market-cost-value");
            var old_form = document.getElementById("old-form");
            old_form.setAttribute("value", ol.id);
            var new_form = document.getElementById("new-form");
            new_form.setAttribute("value", ne.getAttribute("id_player"));
            var balance = item.getAttribute("data-value");

            var value = parseInt(balance) + parseInt(ol.getAttribute("data-value")) - parseInt(ne.getAttribute("data-value"));
            item.innerHTML = value;
            if (value < 0) disable_but(true);
            else disable_but(false);
        }
    };

    var ol = null,ne = null;

    var change_market = function (obj, id) {
        var players = document.getElementsByClassName("market-player");
        if (id == "market-old") {
            var table_new = document.getElementById("free-table");
            table_new.style.display = "block";
            document.getElementById("market-new").innerHTML = "";
            select_role_market(obj.getAttribute("role"));
        } else if (id == "market-new") {

        }


        for (var i = 0; i < players.length; ++i) {
            var item = players[i];


            if (item.id == id) {
                item.innerHTML = obj.innerHTML;
                cost_change_market();
            }
        }
    };

    var remove_roster = function (pass) {
        document.getElementById("search-element").value = "";
        //list.filter();
        //list.search();
        var obj = pass.currentTarget;
        var free_table = document.getElementById("list");
        var id = +obj.id + "_free";
        var table_element = document.getElementById(id);
        


        var element = document.getElementsByClassName('but-over');
        var role_select;
        var role;
        for (i = 0; i < element.length; ++i) {
            if (element[i].style.display == "none") {
	            role = null;
            } else {
                role = element[i].parentNode.getElementsByClassName("but-role")[0].innerHTML;
            }

        }
        

		if(table_element==null){
			
		}else if (role==null || role.toLowerCase() == table_element.getAttribute("role").toLowerCase()) {
            table_element.style.display = "block";
            table_element.setAttribute("in-roster", "none");
        } else {
            table_element.setAttribute("in-roster", "none");
        }
        
        var index = obj.rowIndex;

        obj.parentNode.removeChild(obj);
        cost_change_create(obj.getAttribute("data-value"), false);

    };

    var add_roster = function (pass) {
        var obj = pass.currentTarget;
        var table = document.getElementById(obj.getAttribute("role") + "_free");
        var value = obj.getAttribute("data-value");

        if ((balance() - parseInt(value)) >= 0) {
            var max = parseInt(table.getAttribute("max"));
            var lenght_table = table.getElementsByClassName("old-player").length;
            
            if (lenght_table < max) {
                var row = document.createElement('div');
                row.className = "old-player";
                row.id = obj.getAttribute("id_player");
				
				console.log(obj);
				
                row.setAttribute("data-value", obj.getAttribute("data-value"));
                row.setAttribute("name", obj.getAttribute("name"));
                row.setAttribute("team", obj.getAttribute("team"));
                //row.setAttribute("onclick","javascript:alert(1);");
                var rolecell = document.createElement('div');
                rolecell.innerHTML = "<span class=\"" + obj.getAttribute("role").toLowerCase() + "-but\">" + obj.getAttribute("role") + "</span>";
                rolecell.className = "role-icon";

                var namecell = document.createElement('div');
                namecell.innerHTML = obj.getAttribute("name");
                namecell.className = "name-player-item";
                
                var valuecell = document.createElement('div');
                valuecell.innerHTML = obj.getAttribute("data-value");
                valuecell.className = "value-player-item";
                
                var teamcell = document.createElement('div');
                teamcell.innerHTML = obj.getAttribute("team");
                teamcell.className = "team-player-item";
                
                var infocell = document.createElement('div');
                infocell.className = "info-player-item";
                
                
                row.appendChild(rolecell);
                row.appendChild(namecell);
                row.appendChild(infocell);
                
                infocell.appendChild(teamcell);
                infocell.appendChild(valuecell);
                
                table.appendChild(row);
                
                obj.style.display = "none";
                obj.setAttribute("in-roster", "yes");
                cost_change_create(value, true);
            };
        };

    };





    var getValues = function () {
        var jsonObj = [];
        var jsonString;
        var table = document.getElementById("P_free");
        for (var r = 0, n = table.getElementsByClassName("old-player").length; r < n; r++) {
            var item = table.getElementsByClassName("old-player")[r].getAttribute("id");
            jsonObj.push(item);
        };

        var table = document.getElementById("D_free");
        for (var r = 0, n = table.getElementsByClassName("old-player").length; r < n; r++) {
            var item = table.getElementsByClassName("old-player")[r].getAttribute("id");
            jsonObj.push(item);
        };

        var table = document.getElementById("C_free");
        for (var r = 0, n = table.getElementsByClassName("old-player").length; r < n; r++) {
            var item = table.getElementsByClassName("old-player")[r].getAttribute("id");
            jsonObj.push(item);
        };

        var table = document.getElementById("A_free");
        for (var r = 0, n = table.getElementsByClassName("old-player").length; r < n; r++) {
            var item = table.getElementsByClassName("old-player")[r].getAttribute("id");
            jsonObj.push(item);
        };

        jsonString = JSON.stringify(jsonObj);

        var url = 'createroster.php';
        var text = '<form action="' + url + '" method="post">';

        for (var i = 0, n = jsonObj.length; i < n; i++) {
            text = text + '<input type="hidden" name="ids[]" value="' + jsonObj[i] + '" />';
        }
        var form = $(text + '</form>');

        $('body').append(form); // This line is not necessary
        $(form).submit();

    };

    var countTo = function(date){
        $('#clock').county({ endDateTime: new Date(date), reflection: false, animation: 'scroll', theme: 'red' });
    };


Element.prototype.remove = function() {
    this.parentElement.removeChild(this);
}

NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
    for(var i = 0, len = this.length; i < len; i++) {
        if(this[i] && this[i].parentElement) {
            this[i].parentElement.removeChild(this[i]);
        }
    }
}