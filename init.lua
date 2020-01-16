-- D1 is GPIO5 --
state_pin= 1
gpio.mode(state_pin, gpio.INPUT)
if gpio.read(state_pin)==1 then
	print("turn on")
	dofile("connection.lua")
else
	print("turn off")
end
