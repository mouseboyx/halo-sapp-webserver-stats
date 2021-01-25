api_version = "1.9.0.0"

function OnScriptLoad()


register_callback(cb['EVENT_SCORE'],"OnPlayerScore")

end

function OnPlayerScore(a)
   say_all(tostring(a))
   say_all(tostring(get_var(a,"$score")))
   
end
