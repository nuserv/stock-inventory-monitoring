import random
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.common.exceptions import TimeoutException, WebDriverException
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from time import sleep
from fake_useragent import UserAgent
from selenium.webdriver import ActionChains
import keyboard
from selenium.common.exceptions import NoSuchElementException
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.action_chains import ActionChains

# Prompt the user for their mobile number and OTP PIN
phone_number = input("Enter your mobile number without 0: ")
first_name = "Matthew"
middle_name = "John"
last_name = "Martinez"
zipcode = "4114"
ID_Number = "010204828725"
unit_number = "blk 5 Lot 76"
street_number = "N/A"
subd_location = "Avida Residences"

user_agent = UserAgent()
chrome_options = Options()
chrome_options.add_argument("--headless") # run Chrome in headless mode
chrome_options.add_argument('--no-sandbox')
chrome_options.add_argument("--window-size=750,1500")
chrome_options.add_argument(f'user-agent={user_agent.random}')
chrome_options.add_argument("--start-maximized")
# chrome_options = Options()
# chrome_options.add_argument('--headless')
# chrome_options.add_argument('--no-sandbox')
# chrome_options.add_argument('--disable-dev-shm-usage')
# Set up the browser driver (I'm using Chrome)
driver = webdriver.Chrome('/usr/local/bin/chromedriver',chrome_options=chrome_options)

# Navigate to the website
url = "https://new.globe.com.ph/simreg"
driver.get(url)

# # Wait for the OTP input field to appear
# wait = WebDriverWait(driver, 10)
# otp_field = wait.until(EC.presence_of_element_located((By.ID, "otpMsisdnInput")))
# accept_button = driver.find_element(By.CSS_SELECTOR, "button[_ngcontent-globe-estore-frontend-c111='']")
# accept_button.click()

# # Find the input field by its ID and fill in the phone number
# otp_field.send_keys(phone_number)
# otp_field.send_keys(Keys.RETURN)

# # Wait for the "Register" button to appear
# register_button = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, ".go_sc-otp__get-code-btn")))
# # Find the "Register" button and click it
# register_button.click()

# # Wait for the OTP fields to appear
# otp_fields = wait.until(EC.presence_of_all_elements_located((By.CSS_SELECTOR, ".gk-otp-fields__input")))

# otp = input("Enter the OTP: ")
# for i in range(len(otp)):
#     otp_fields[i].send_keys(otp[i])
# print("PROCESSING.... PLEASE WAIT!!")
# # Wait for the form fields to appear
