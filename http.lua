
api_version = "1.9.0.0"
--[[
-----Configuration-----
]]--

--Place server key here
    server_key="development_key_123"

--The full path of the webserver running statiscs api with trailing slash
--Example: https://www.an-example-domain-name.com/halo_stats/
    domain_name="http://192.168.86.24/halo/"

--If a single http request takes this many seconds to complete assume the webserver is down or something went wrong
    request_fail_after=30
    --ACTION TO TAKE if a request fails for this amount of time
    --If set to true stop all logging and try to start it again on a new game
    --If set to false then ignore the timeout and try the next request in line
    stop_log=false
    

-----End Configuration-----

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
lastHitString={};
tempMap='';

--Code to find damage "!jpt" tag paths thanks to pR0Ps https://github.com/pR0Ps/halo-ce-sapp-scripts/blob/master/gungame.lua
local DATA = nil
function GenerateReferenceData()
    -- Get tags for every damage available in the map
    local damage_tag_data = get_damage_tag_data()
    local damage_tag_by_dmg ={}
    for _, damage in ipairs(damage_tag_data) do
        local damage_tag_type = damage[1]
        local damage_tag_path = damage[2]
        local damage_tag_id = get_tag_id(damage_tag_type, damage_tag_path)
        cprint(string.format(" - %s: %s : %s", damage_tag_type, damage_tag_path, damage_tag_id))
        damage_tag_by_dmg[damage_tag_id]=damage_tag_path
        
    end
    DATA = {
        damage_tag_by_dmg = damage_tag_by_dmg,        
    }
end
--end block given by pR0Ps


function OnScriptLoad()


register_callback(cb['EVENT_SPAWN'],"OnPlayerSpawn")
register_callback(cb['EVENT_GAME_START'],"OnNewGame")
register_callback(cb['EVENT_GAME_END'],"OnGameEnd")
register_callback(cb["EVENT_TICK"],"OnTick")
register_callback(cb['EVENT_DIE'], 'OnPlayerDeath')
register_callback(cb['EVENT_DAMAGE_APPLICATION'], "OnDamageApplication")
register_callback(cb['EVENT_SCORE'],"OnPlayerScore")
end

function OnPlayerScore(PlayerIndex)
   --say_all(tostring(a))
   --say_all(tostring(get_var(a,"$score")))
   if (game_started==true) then
        local playerName=encodeString(get_var(PlayerIndex,"$name"));
        local playerScore=encodeString(get_var(PlayerIndex,"$score"));
        local playerIp = encodeString(get_var(PlayerIndex, "$ip"):match("(%d+.%d+.%d+.%d+)"))
        local server_key_request='&key='..encodeString(server_key)
        local url_text=domain_name.."halo.php?scoring=1&name="..playerName.."&ip="..playerIp.."&score="..playerScore..server_key_request
        --say_all(url_text);
                table.insert(response, http_client.http_get(url_text,true))
                table.insert(response_url,url_text)
   end
end

function OnDamageApplication(PlayerIndex, Causer, MetaID, Damage, HitString, Backtap)
	say_all(PlayerIndex..","..Causer..","..HitString..","..MetaID)
	meta_id=tostring(MetaID)
    pi=tonumber(PlayerIndex);
    lastDamagedBy[pi]=MetaID;
    lastHitString[pi]=HitString;
	--say_all(tostring(DATA.damage_tag_by_dmg[MetaID]))
	if (Causer~=0) then
	return true, Damage
	end
end




function OnPlayerDeath(PlayerIndex, KillerIndex)
		local killer = tonumber(KillerIndex)
		local victim = tonumber(PlayerIndex)
        --say_all(tostring(DATA.damage_tag_by_dmg[lastDamagedBy[victim]]))
		--say_all(killer..","..victim)
	if (game_started==true) then
		if (killer~=-1 and killer~=0 and killer~=victim) then
            local killed_by_weapon =encodeString(tostring(DATA.damage_tag_by_dmg[lastDamagedBy[victim]]))
            local killer_name=encodeString(get_var(killer,"$name"))
            local killer_ip = encodeString(get_var(killer, "$ip"):match("(%d+.%d+.%d+.%d+)"))
            local victim_name=encodeString(get_var(victim,"$name"))
            local victim_ip = encodeString(get_var(victim, "$ip"):match("(%d+.%d+.%d+.%d+)"))
            local server_key_request='&key='..encodeString(server_key)
            local body_part=encodeString(lastHitString[victim]);
            if (stop_http_requests==false) then
               local url_text=domain_name.."halo.php?killer="..killer_name.."&killer_ip="..killer_ip.."&victim="..victim_name.."&victim_ip="..victim_ip.."&killed_by_weapon="..killed_by_weapon.."&body_part="..body_part..server_key_request
                table.insert(response, http_client.http_get(url_text,true))
                table.insert(response_url,url_text)
            end
        end
    end
end

function OnNewGame()
  		--need to add a http request here to let the webserver know a new game has started for this particular server
        game_started = true
        stop_http_requests=false
        GenerateReferenceData()
        local server_key_request='&key='..encodeString(server_key)
        local current_map=encodeString(tostring(get_var(1,"$map")));
        local current_mode=encodeString(tostring(get_var(1,"$mode")));
        local current_game_type=encodeString(tostring(get_var(1,"$gt")));
        url_text=domain_name.."game.php?newgame=1".."&type="..current_game_type.."&mode="..current_mode.."&map="..current_map..server_key_request
        if (stop_http_requests==false) then
            table.insert(response, http_client.http_get(url_text,true))
            table.insert(response_url,url_text)
        end
        timer(1000,"getTestPage")
        
		
end   

function OnGameEnd()
        game_started = false
end


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

stop_http_requests=false
response={}
response_url={}
http_fail_count=0;
function getTestPage()
	local stopTimer=false
	if (response[1]~=nil) then
        --say_all("null:"..tostring(http_client.http_response_is_null(response[1])))
        --say_all("length:"..tostring(http_client.http_response_length(response[1])))
        --say_all("received:"..tostring(http_client.http_response_received(response[1])))
        if (http_client.http_response_received(response[1])==false) then
            local url_text=response_url[1]
            say_all(tostring(response_url[1]).." waiting "..http_fail_count)
            --http_client.http_destroy_response(response[1])
            --spliced, remainder = table.splice(response,1,1)
            --response=spliced;
            --spliced, remainder = table.splice(response_url,1,1)
            --response_url=spliced;
            http_fail_count=http_fail_count+1;
            if (stop_log==false) then
                if (http_fail_count>request_fail_after) then
                    say_all(tostring("Ignoring timeout for "..response_url[1]))
                    http_client.http_destroy_response(response[1])
                    spliced, remainder = table.splice(response,1,1)
                    response=spliced;
                    spliced, remainder = table.splice(response_url,1,1)
                    http_fail_count=0;
                    
                end
            end
            --table.insert(response, http_client.http_get(url_text,true))
            --table.insert(response_url,url_text)
            
        else 
            if http_client.http_response_is_null(response[1]) ~= true then
            --say_all("null:"..tostring(http_client.http_response_is_null(response[1])))
            --say_all("length:"..tostring(http_client.http_response_length(response[1])))
            --say_all("received:"..tostring(http_client.http_response_received(response[1])))
            local response_text_ptr = http_client.http_read_response(response[1])
            returning = ffi.string(response_text_ptr)
                http_client.http_destroy_response(response[1])
                --say_all(tostring(table.getn(response)))
                spliced, remainder = table.splice(response,1,1)
                response=spliced;
                spliced, remainder = table.splice(response_url,1,1)
                response_url=spliced;
                http_fail_count=0;
                --say_all(tostring(table.getn(response)))
                say_all(returning)
                if (game_started==false and table.getn(response)==0) then
                    stopTimer=true
                end
            else
                
                
            end
        end
	end
    if (stop_log==true) then
        if (http_fail_count>request_fail_after) then
            say_all("Failed to send http data "..request_fail_after.." times on a single request, stopping log")
            stop_http_requests=true
            stopTimer=true
            for key, value in pairs(response) do
                http_client.http_destroy_response(response[key])
                
            end
            response_url={};
        end
    end
    
	if (stopTimer==false) then
		timer (250,"getTestPage")
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
                        local url_text="http://192.168.86.24/halo/score.php"
                        table.insert(response,http_client.http_get(url_text,true))
                        table.insert(response_url,url_text);
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

--begin helper functions for GenerateReferenceData() given by pR0Ps
-- Decode an integer to an ascii string
function decode_ascii(value)
    local r, i = {}, 1
    while value > 0 do
        r[i] = string.char(value%256)
        i = i + 1
        value = math.floor(value/256)
    end
    return string.reverse(table.concat(r))
end


-- Reads the tag type and path from an address
function resolve_reference(address)
    local type = decode_ascii(read_dword(address))
    local path_address = read_dword(address + 0x4)
    if path_address == 0 then
        return type, nil
    end
    return type, read_string(path_address)
end





function get_damage_tag_data()
    local r = {}
    local map_base = 0x40440000
    local tag_base = read_dword(map_base)
    local tag_count = read_dword(map_base + 0xC)

    for i=0, tag_count - 1 do
        local tag = tag_base + i * 0x20
        local tag_type = decode_ascii(read_dword(tag))
        local tag_path = read_string(read_dword(tag + 0x10))
        if (tag_type == "jpt!") then
            r[#r+1] = {tag_type, tag_path}
        end
    end
    return r
end


-- Get the id of a tag given its type and path
function get_tag_id(tag_type, tag_path)
    local tag = lookup_tag(tag_type, tag_path)
    if tag == 0 then return nil end
    return read_dword(tag + 0xC)
end


-- Convert tag path keys of a table to tag ids
-- Drops invalid tags
function tagmap(type, tbl)
    local r = {}
    for k, v in pairs(tbl) do
        local id = get_tag_id(type, k)
        if id ~= nil then
            r[id] = v
        end
    end
    return r
end
--end block by pR0Ps
