from __future__ import annotations

import os
import random
import subprocess
import string
import socket
import sys
import time
from pathlib import Path

from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait


DEFAULT_URL = "http://127.0.0.1:8030/login"
DEFAULT_HOST = "127.0.0.1"
DEFAULT_PORT = 8030


def port_is_open(host: str, port: int) -> bool:
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as sock:
        sock.settimeout(0.5)
        return sock.connect_ex((host, port)) == 0


def start_laravel_server(project_root: Path, host: str, port: int) -> subprocess.Popen[str] | None:
    if port_is_open(host, port):
        return None

    env = os.environ.copy()
    env["APP_URL"] = f"http://{host}:{port}/"

    router_code = """<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . '/public' . $path;

if ($path !== '/' && is_file($file)) {
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $contentTypes = [
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'json' => 'application/json; charset=UTF-8',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
    ];

    if (isset($contentTypes[$extension])) {
        header('Content-Type: ' . $contentTypes[$extension]);
    }

    readfile($file);
    return;
}

require __DIR__ . '/public/index.php';
"""

    router_path = project_root / "selenium_router.php"
    router_path.write_text(router_code, encoding="utf-8")

    return subprocess.Popen(
        ["php", "-S", f"{host}:{port}", "-t", "public", "selenium_router.php"],
        cwd=project_root,
        env=env,
        stdout=subprocess.DEVNULL,
        stderr=subprocess.DEVNULL,
        text=True,
    )


def random_value(prefix: str, length: int = 8) -> str:
    alphabet = string.ascii_lowercase + string.digits
    suffix = "".join(random.choice(alphabet) for _ in range(length))
    return f"{prefix}{suffix}"


def main() -> int:
    project_root = Path(__file__).resolve().parent
    server_process = start_laravel_server(project_root, DEFAULT_HOST, DEFAULT_PORT)

    target_url = sys.argv[1] if len(sys.argv) > 1 else DEFAULT_URL
    email = f"{random_value('user_')}@example.com"
    password = random_value('Passw0rd_', 12)

    options = webdriver.ChromeOptions()
    options.add_argument("--start-maximized")

    driver = webdriver.Chrome(options=options)

    try:
        print("Opening the Laravel login page with styles enabled.")
        driver.get(target_url)

        wait = WebDriverWait(driver, 20)
        wait.until(lambda browser: browser.execute_script("return document.readyState") == "complete")

        email_field = wait.until(EC.visibility_of_element_located((By.NAME, "email_address")))
        password_field = wait.until(EC.visibility_of_element_located((By.NAME, "password")))

        email_field.click()
        email_field.clear()
        email_field.send_keys(email)

        password_field.click()
        password_field.clear()
        password_field.send_keys(password)

        driver.execute_script(
            """
            const email = arguments[0];
            const password = arguments[1];
            const emailField = document.querySelector('[name="email_address"]');
            const passwordField = document.querySelector('[name="password"]');

            if (emailField && emailField.value !== email) {
                emailField.value = email;
                emailField.dispatchEvent(new Event('input', { bubbles: true }));
                emailField.dispatchEvent(new Event('change', { bubbles: true }));
            }

            if (passwordField && passwordField.value !== password) {
                passwordField.value = password;
                passwordField.dispatchEvent(new Event('input', { bubbles: true }));
                passwordField.dispatchEvent(new Event('change', { bubbles: true }));
            }
            """,
            email,
            password,
        )

        filled_email = driver.find_element(By.NAME, "email_address").get_attribute("value")
        filled_password = driver.find_element(By.NAME, "password").get_attribute("value")

        if filled_email != email or filled_password != password:
            raise RuntimeError("Selenium could not confirm the login fields were filled")

        time.sleep(1)
        print(f"Filled login form at {target_url}")
        print(f"Email: {email}")
        print(f"Password: {password}")
        return 0
    finally:
        driver.quit()
        if server_process is not None:
            server_process.terminate()


if __name__ == "__main__":
    raise SystemExit(main())