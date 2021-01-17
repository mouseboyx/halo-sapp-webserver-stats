
api_version = "1.9.0.0"
--[[
-----Configuration-----
]]--

--Place server key here
server_key="development_key_123"

--The full path of the webserver running statiscs api with trailing slash
--Example: https://www.an-example-domain-name.com/halo_stats/
domain_name="http://192.168.86.24/halo/"

-----End Configuration-----
allWeapons={"weapons\\assault rifle\\assault rifle","weapons\\ball\\ball","weapons\\flag\\flag","weapons\\flamethrower\\flamethrower","weapons\\plasma_cannon\\plasma_cannon","weapons\\needler\\mp_needler","weapons\\pistol\\pistol","weapons\\plasma pistol\\plasma pistol","weapons\\plasma rifle\\plasma rifle","weapons\\rocket launcher\\rocket launcher","weapons\\shotgun\\shotgun","weapons\\sniper rifle\\sniper rifle"}
allWeaponsIndex=1;
ffi = require("ffi")
ffi.cdef [[
    typedef void http_response;
    http_response *http_get(const char *url, bool async);
    void http_destroy_response(http_response *);
    void http_wait_async(const http_response *);
    bool http_response_is_null(const http_response *);
    bool http_response_received(const http_response *);
    const char *http_read_response(const http_response *);
    uint32_t http_response_length(const http_response *);
]]
http_client = ffi.load("lua_http_client")
game_started = false

ACTIONS = {
	["shooting"] = 0,
	["grenade"] = 0,
	["crouch"] = 0,
	["jump"] = 0,
	["flashlight"] = 0,
	["action"] = 0,
	["melee"] = 0,
	["reload"] = 0,
	["nade_switch"] = 0,
	["weapon_switch"] = 0,
	["zoom"] = 0,
	["front"] = 0,
	["back"] = 0,
	["left"] = 0,
	["right"] = 0,
}

previous_zoom_level = {}
previous_anim_state = {}
previous_crouch_state = {}
previous_jump_state = {}
previous_nade_type = {}
previous_weapon_slot = {}
previous_reload_state = {}
previous_shooting_state = {}

lastDamagedBy={};
tempMap='';
function OnScriptLoad()


register_callback(cb['EVENT_SPAWN'],"OnPlayerSpawn")
register_callback(cb['EVENT_GAME_START'],"OnNewGame")
register_callback(cb['EVENT_GAME_END'],"OnGameEnd")
register_callback(cb["EVENT_TICK"],"OnTick")
register_callback(cb['EVENT_DAMAGE_APPLICATION'], "OnDamageApplication")
end

function OnDamageApplication(PlayerIndex, Causer, MetaID, Damage, HitString, Backtap)
	say_all(PlayerIndex..","..Causer..","..HitString..","..MetaID)
	meta_id=tostring(MetaID)
    pi=tonumber(PlayerIndex);
    lastDamagedBy[pi]=meta_id;
	
	if (Causer~=0) then
	return false, Damage*0
	end
end




function OnNewGame()
  		--need to add a http request here to let the webserver know a new game has started for this particular server
        game_started = true
        
		
end   

function OnGameEnd()
        game_started = false
end


response={}
--on player spawn is just a test it could be put anywhere
function OnPlayerSpawn(PlayerIndex)
	previous_zoom_level[PlayerIndex] = 65535
	previous_anim_state[PlayerIndex] = nil
	previous_crouch_state[PlayerIndex] = 0
	previous_jump_state[PlayerIndex] = 0
	previous_nade_type[PlayerIndex] = 0
	previous_weapon_slot[PlayerIndex] = 0
	previous_reload_state[PlayerIndex] = 0
	previous_shooting_state[PlayerIndex] = 0
	
		
	
end



function getTestPage()
	local stopTimer=false
	if (response[1]~=nil) then	
		if http_client.http_response_is_null(response[1]) ~= true then
		local response_text_ptr = http_client.http_read_response(response[1])
		returning = ffi.string(response_text_ptr)
			http_client.http_destroy_response(response[1])
			--say_all(tostring(table.getn(response)))
			spliced, remainder = table.splice(response,1,1)
			response=spliced;
			--say_all(tostring(table.getn(response)))
			say_all(returning)
			if (game_started==false and table.getn(response)==0) then
				stopTimer=true
			end
		else
			
			
		end
	end
	if (stopTimer==false) then
		timer (1000,"getTestPage")
	end
    

end



function OnTick()
	local current_zoom_level
	local current_anim_state
	local current_crouch_state
	local current_jump_state
	local current_nade_type
	local current_weapon_slot
	local current_reload_state
	local current_shooting_state
	
	for i=1,16 do
		if(player_alive(i)) then
			local player = get_dynamic_player(i)
			
			ACTIONS["flashlight"] = read_bit(player + 0x208,4)
			ACTIONS["action"] = read_bit(player + 0x208,6)
			ACTIONS["melee"] = read_bit(player + 0x208, 7) 
			
			current_shooting_state = read_float(player + 0x490)
			if(current_shooting_state ~= previous_shooting_state[i] and current_shooting_state == 1) then
				ACTIONS["shooting"] = 1
			else
				ACTIONS["shooting"] = 0
			end
			previous_shooting_state[i] = current_shooting_state
			
			current_reload_state = read_byte(player + 0x2A4)
			if(previous_reload_state[i] ~= current_reload_state and current_reload_state == 5) then
				ACTIONS["reload"] = 1
			else
				ACTIONS["reload"] = 0
			end
			previous_reload_state[i] = current_reload_state
			
			current_crouch_state = read_bit(player + 0x208,0)
			if(current_crouch_state ~= previous_crouch_state[i] and current_crouch_state == 1) then
				ACTIONS["crouch"] = 1
			else
				ACTIONS["crouch"] = 0
			end
			previous_crouch_state[i] = current_crouch_state
			
			current_jump_state = read_bit(player + 0x208,1)
			if(current_jump_state ~= previous_jump_state[i] and current_jump_state == 1) then
				ACTIONS["jump"] = 1
			else
				ACTIONS["jump"] = 0
			end
			previous_jump_state[i] = current_jump_state
			
			current_nade_type = read_byte(player + 0x47E)
			if(current_nade_type ~= previous_nade_type[i]) then
				ACTIONS["nade_switch"] = 1
			else
				ACTIONS["nade_switch"] = 0
			end
			previous_nade_type[i] = current_nade_type
			
			current_weapon_slot = read_byte(player + 0x47C)
			if(current_weapon_slot ~= previous_weapon_slot[i]) then
				ACTIONS["weapon_switch"] = 1
			else
				ACTIONS["weapon_switch"] = 0
			end
			previous_weapon_slot[i] = current_weapon_slot
			
			local current_anim_state = read_byte(player + 0x2A3)
			if(current_anim_state ~= previous_anim_state[i]) then
				if(current_anim_state == 4) then ACTIONS["front"] = 1 end
				if(current_anim_state == 5) then ACTIONS["back"] = 1 end
				if(current_anim_state == 6) then ACTIONS["left"] = 1 end
				if(current_anim_state == 7) then ACTIONS["right"] = 1 end
				if(current_anim_state == 33) then ACTIONS["grenade"] = 1 end
			else
				ACTIONS["front"] = 0
				ACTIONS["back"] = 0
				ACTIONS["left"] = 0
				ACTIONS["right"] = 0
				ACTIONS["grenade"] = 0
			end
			previous_anim_state[i] = current_anim_state
			
			current_zoom_level = read_word(player + 0x480)
			if(current_zoom_level ~= previous_zoom_level[i]) then
				ACTIONS["zoom"] = 1
			else
				ACTIONS["zoom"] = 0
			end
			previous_zoom_level[i] = current_zoom_level

			
			
			for key,value in pairs(ACTIONS) do
				if(value ~= 0) then
					--execute_command("say " .. i .. " \"" .. math.random() .. "\"")
					--rprint(i, player_weapon[PlayerIndex]) --	~CALL A FUNCTION OR SOMETHING HERE~
					--execute_command("say " .. i .. " \"" .. player_weapon[i] .. "\"")
                    --[[
                    
                        I'm using this section to debug things so that if a player presses the flashlight key information can be sent to the halo client
                    ]]--
					if(key == "flashlight") then
						--instring=encodeString(get_var(i,"$name"))
						--[[
						outstring=''
						for i = 1, string.len(instring) do
						    local c = instring:sub(i,i)
						    testchar=string.char(string.byte(c))
							outstring=outstring..testchar
						    --say_all(tostring(string.char(testchar)))
						end						
						say_all(outstring)]]--
						execute_command("wdel "..i.." 5");
                        execute_command("spawn weap '"..allWeapons[allWeaponsIndex].."' "..i.." 0")
                        execute_command("wadd "..i)
                        say_all(tostring(get_var(i,"$invis")))
                        allWeaponsIndex=allWeaponsIndex+1
                        if (allWeaponsIndex>12) then
                            allWeaponsIndex=1
                        end
						--teststring=string.char(testchar);
						--say_all(" sent "..tostring(testchar).." , "..tostring(teststring))
						--responseSize=table.getn(response)
						--response[responseSize+1] = http_client.http_get("http://192.168.86.24/sleep.php",true)	
						--table.insert(response, http_client.http_get("https://mouseboyx.xyz/",true))
                        --say_all(tempMap)
						--Check after 1 second to see if the request has been completed
						--timer (1000,"getTestPage")
					end
					--spawn weap "weapons\pistol\pistol" $n
					
					
				end
			end
			
		end
	end
end

function encodeChar(chr)
	return string.format("%%%X",string.byte(chr))
end
 
function encodeString(str)
	local output, t = string.gsub(str,"[^%w]",encodeChar)
	return output
end

function table.splice(tbl, start, length)
   length = length or 1
   start = start or 1
   local endd = start + length
   local spliced = {}
   local remainder = {}
   for i,elt in ipairs(tbl) do
      if i < start or i >= endd then
         table.insert(spliced, elt)
      else
         table.insert(remainder, elt)
      end
   end
   return spliced, remainder
end
