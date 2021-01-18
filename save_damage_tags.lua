--[[

Adapted from https://github.com/pR0Ps/halo-ce-sapp-scripts/blob/master/gungame.lua
Thanks to pR0Ps
LICENCE: GNU GPL3
]]--

----------------- Script ------------------

api_version = "1.11.0.0"

function file_exists(name)
   local f=io.open(name,"r")
   if f~=nil then io.close(f) return true else return false end
end

local DATA = nil
function GenerateReferenceData()
    -- Get tags for every damage available in the map
    local damage_tag_data = get_damage_tag_data()
    
   
    local damage_tag_paths = {}
    local damage_tag_ids = {}
    local damage_tag_by_dmg ={}
    local file = io.open("sapp\\damage_tags.txt", "a")
    file:write("---Begin All Damage Tags "..tostring(get_var(1,"$map")).."---\n");
    file:close()
    
    for _, damage in ipairs(damage_tag_data) do
        local damage_tag_type = damage[1]
        local damage_tag_path = damage[2]
        local damage_tag_id = get_tag_id(damage_tag_type, damage_tag_path)

        damage_tag_ids[damage_tag_id] = true
        damage_tag_paths[#damage_tag_paths+1] = damage_tag_path

        -- Uncomment this if you want a list of weapon tags the current map
        -- supports to be printed to the console
        cprint(string.format(" - %s: %s : %s", damage_tag_type, damage_tag_path, damage_tag_id))
        --already_wrote=file_exists("sapp\\damage_tags.txt")
        --if (already_wrote==false) then
            local file = io.open("sapp\\damage_tags.txt", "a")
            
        
            
            file:write(tostring(damage_tag_path).."\n");
        
            
            
            
            file:close()
        --end
        damage_tag_by_dmg[damage_tag_id]=damage_tag_path
        
    end
    local file = io.open("sapp\\damage_tags.txt", "a")
    file:write("---End All Damage Tags "..tostring(get_var(1,"$map")).."---\n");
    file:write("---Begin In Game Damage Tags "..tostring(get_var(1,"$map")).."---\n");
    file:close()
    DATA = {
        
        damage_tag_by_dmg = damage_tag_by_dmg,
        
        
    }
end

lastDamagedBy={};
function OnScriptLoad()

    register_callback(cb['EVENT_DIE'], 'OnPlayerDie')
    register_callback(cb['EVENT_DAMAGE_APPLICATION'], "OnDamageApplication")
    register_callback(cb['EVENT_GAME_START'],"OnNewGame")
    register_callback(cb["EVENT_CHAT"], "OnPlayerChat")
end

function OnNewGame() 
    GenerateReferenceData()
end
function OnPlayerChat(PlayerIndex, Message, MessageType)
        local file = io.open("sapp\\damage_tags.txt", "a")
        file:write("Chat: "..Message.."\n");
        file:close()
end
function OnDamageApplication(player_index, causer_index, damage_tag_id, damage, ...)
    pi=tonumber(player_index)
    lastDamagedBy[pi]=damage_tag_id;
    
    --player_data[player_index].last_dmg_tag = damage_tag_id
    
   
    say_all("Damage: "..tostring(DATA.damage_tag_by_dmg[damage_tag_id]))
    local file = io.open("sapp\\damage_tags.txt", "a")
    file:write("Damage: "..tostring(DATA.damage_tag_by_dmg[damage_tag_id]).."\n")
    file:close()
    cprint(string.format("%s", DATA.damage_tag_by_dmg[damage_tag_id]))
    return true, damage
    
end


function OnPlayerDie(player_index, killer_index)
    pi=tonumber(player_index);
  
    say_all("Death: "..tostring(DATA.damage_tag_by_dmg[lastDamagedBy[pi]]))
    --cprint("%s","Death:")
    local file = io.open("sapp\\damage_tags.txt", "a")
    file:write("Death: "..tostring(DATA.damage_tag_by_dmg[lastDamagedBy[pi]]).."\n")
    file:close()
    cprint(string.format("%s", DATA.damage_tag_by_dmg[lastDamagedBy[pi]]))
  


   
       
       
end





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





-- Pull out all weapons and eqipment whos tag starts with "weapons\" 
-- For each tag return a list of:
-- {<tag type>, <tag path>, {<damage effect(s) this can cause>}}
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
