# JodTod Mobile App - Troubleshooting Guide

Common issues encountered during development and their fixes.

---

## 1. App stuck on splash/logo screen (won't navigate to login)

**Cause:** `.env` file has wrong IP address. Mobile phone can't reach the Laravel API server.

**Fix:**
1. Run `ipconfig` in terminal to get your PC's current WiFi IPv4 address
2. Update `D:\JodTodApp\.env`:
   ```
   EXPO_PUBLIC_API_URL=http://<YOUR_PC_IP>:8001
   ```
3. Restart expo: `npx expo start --clear`

**Note:** IP changes when you switch WiFi networks. Always verify with `ipconfig`.

---

## 2. "Unable to resolve module react-dom/client"

**Cause:** Expo 55's `@expo/log-box` internally imports `react-dom` even on native platforms.

**Fix:**
```bash
cd D:\JodTodApp
npm install react-dom --legacy-peer-deps
npx expo start --clear
```

One-time fix. Won't happen again after installing.

---

## 3. "Unable to resolve module expo-clipboard" (or any expo-* module)

**Cause:** A screen uses an Expo package that isn't installed.

**Fix:**
```bash
cd D:\JodTodApp
npm install expo-clipboard --legacy-peer-deps
npx expo start --clear
```

Always restart with `--clear` after installing new packages.

---

## 4. Windows Firewall blocks mobile from reaching Laravel API

**Cause:** Windows Firewall blocks incoming connections on port 8001 by default. PC can access it locally but mobile phone on same WiFi can't.

**Fix:** Run this in **PowerShell (Run as Administrator)** — one time only:
```powershell
netsh advfirewall firewall add rule name="Laravel Dev Server" dir=in action=allow protocol=TCP localport=8001
```

---

## 5. Expo "Port XXXX is being used by another process"

**Cause:** Previous expo server wasn't stopped properly.

**Fix:** Either:
- Press `Y` to use alternate port (won't affect anything)
- Or kill the port: `npx kill-port 8081`
- Or find and close the previous terminal running expo

---

## 6. Changes not reflecting after npm install

**Cause:** Metro bundler caches old dependency tree.

**Fix:** Always restart with cache clear after installing packages:
```bash
npx expo start --clear
```

If still stuck:
```bash
rm -rf node_modules/.cache
npx expo start --clear
```

Nuclear option (rarely needed):
```bash
rm -rf node_modules
npm install --legacy-peer-deps
npx expo start --clear
```

---

## Dev Setup Checklist (for new machine / fresh start)

1. `cd D:\JodTodApp && npm install --legacy-peer-deps`
2. Get PC IP: `ipconfig` → find IPv4 address
3. Update `.env`: `EXPO_PUBLIC_API_URL=http://<IP>:8001`
4. Add firewall rule (admin PowerShell): `netsh advfirewall firewall add rule name="Laravel Dev Server" dir=in action=allow protocol=TCP localport=8001`
5. Start Laravel: `php artisan serve --host=0.0.0.0 --port=8001` (from `D:\phpserver\www\JodTod`)
6. Start Expo: `npx expo start --clear` (from `D:\JodTodApp`)
7. Scan QR code from Expo Go app on phone (same WiFi)

---

## Important Notes

- **`--legacy-peer-deps`** is needed for most npm installs because Expo 55 + React 19 has peer dep conflicts
- **`.env` changes** require expo restart with `--clear` to take effect
- **Phone and PC must be on same WiFi** for development
- **Port 8001** = Laravel backend (API), **Port 8081/8082** = Expo dev server (app code)
