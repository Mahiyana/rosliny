function role_function(o,t){$.post("/change_role/"+t+"/"+o,function(t){console.log(t.role),t.role?($("#"+o+"_status").html("Posiada"),$("#"+o+"_button").html("Zabierz")):($("#"+o+"_status").html("Nie posiada"),$("#"+o+"_button").html("Dodaj"))})}
