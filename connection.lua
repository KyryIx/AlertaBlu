function connetion()
	http.get('http://www.everton.mat.br/alertablu/data.php?option=node',nill,function(code,data)
		if code<0 then
			print("HTTP request failed\n")
		else
			print(code,data)
			decoder=sjson.decoder()
			decoder:write(data)
			result=decoder:result()
			print('          Data[0]: '..result['d'][1])
			print('Periodo do dia[0]: '..result['p'][1])
			print('        Imagem[0]: '..result['i'][1])
			print(' Temperatura min.: '..result['l'])
			print(' Temperatura max.: '..result['u'])
		end
	end)
end

wifi.setmode(wifi.STATION)
station_cfg={}
station_cfg.ssid="Virus..."
station_cfg.pwd="victoriaHelena"
wifi.sta.config(station_cfg)

mytimer1=tmr.create()
mytimer1:register(5000,tmr.ALARM_AUTO,function(t1)
	if wifi.sta.getip()~=nil then
		print(wifi.sta.getip().."\n")
		t1:unregister()
	else
		print("without ip\n")
	end
end)
mytimer1:start()

connetion()

mytimer2=tmr.create()
mytimer2:register(1000,tmr.ALARM_AUTO,function(t2)
	connetion()
end)
mytimer2:start()