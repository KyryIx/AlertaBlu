-- modules: dht, ds18b20, file, gpio, http, i2c, mqtt, net, node, ow, pwm, sjson, spi, tmr, uart, wifi
-- 1 to use D4 (GPIO4) --
function writeNextion(command)
	uart.setup(1,9600,8,uart.PARITY_NONE,uart.STOPBITS_1,1)
	uart.write(1,command)
	for i=1,3 do
		uart.write(1,0xff)
	end
end

function connetion()
	http.get('http://www.everton.mat.br/alertablu/data.php?option=node',nill,function(code,data)
		if code<0 then
			print("HTTP request failed\n")
		else
			print(code,data)
			decoder=sjson.decoder()
			decoder:write(data)
			result = decoder:result()
			print('          Data[0]: '..result['d'][1])
			print('Periodo do dia[0]: '..result['p'][1])
			print('        Imagem[0]: '..result['i'][1])
			print(' Temperatura min.: '..result['l'])
			print(' Temperatura max.: '..result['u'])
			
			writeNextion('t0.txt="'..result['p'][1]..'"')
			writeNextion('t2.txt="'..result['p'][2]..'"')
			writeNextion('t4.txt="'..result['p'][3]..'"')
			writeNextion('t6.txt="'..result['p'][4]..'"')
			
			writeNextion('t1.txt="'..result['d'][1]..'"')
			writeNextion('t3.txt="'..result['d'][2]..'"')
			writeNextion('t5.txt="'..result['d'][3]..'"')
			writeNextion('t7.txt="'..result['d'][4]..'"')
			
			writeNextion('p0.pic='..result['i'][1])
			writeNextion('p1.pic='..result['i'][2])
			writeNextion('p2.pic='..result['i'][3])
			writeNextion('p3.pic='..result['i'][4])
			
			writeNextion('t10.txt="'..result['l']..'"')
			writeNextion('t11.txt="'..result['u']..'"')
			
			writeNextion('t12.txt="'..result['h']..'"')
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
		print(wifi.sta.getip())
		t1:unregister()
	else
		print("without ip\n")
	end
end)
mytimer1:start()

connetion()

mytimer2=tmr.create()
mytimer2:register(60000,tmr.ALARM_AUTO,function(t2)
	connetion()
end)
mytimer2:start()
