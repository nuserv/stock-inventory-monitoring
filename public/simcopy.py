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
phone_number = input("Please input sim number Ex. 9xxxxxxxxx:")
names = ['John', 'Mary', 'Bob', 'Alice', 'Tom', 'Linda']
first_name = random.choice(names)
middle_name = "Pedro"
last_name = "Ortega"
zipcode = "1440"
ID_Number = "182289535634"
unit_number = "blk 5 Lot 76"
street_number = "N/A"
subd_location = "Amana"
print("SENDING OTP.... PLEASE WAIT!!")
user_agent = UserAgent()
chrome_options = Options()
chrome_options.add_argument("--headless") # run Chrome in headless mode
chrome_options.add_argument('--no-sandbox')
chrome_options.add_argument("--window-size=750,1500")
chrome_options.add_argument(f'user-agent={user_agent.random}')
chrome_options.add_argument("--start-maximized")
# Set up the browser driver (I'm using Chrome)
driver = webdriver.Chrome(options=chrome_options)
# driver = webdriver.Chrome()
# driver = webdriver.Chrome('/usr/local/bin/chromedriver', options=chrome_options)

# Navigate to the website
url = "https://new.globe.com.ph/simreg"
driver.get(url)

# Wait for the OTP input field to appear
wait = WebDriverWait(driver, 10)
otp_field = wait.until(EC.presence_of_element_located((By.ID, "otpMsisdnInput")))
accept_button = driver.find_element(By.CSS_SELECTOR, "button[_ngcontent-globe-estore-frontend-c111='']")
accept_button.click()

# Find the input field by its ID and fill in the phone number
otp_field.send_keys(phone_number)
otp_field.send_keys(Keys.RETURN)

# Wait for the "Register" button to appear
register_button = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, ".go_sc-otp__get-code-btn")))
# Find the "Register" button and click it
register_button.click()

# Wait for the OTP fields to appear
otp_fields = wait.until(EC.presence_of_all_elements_located((By.CSS_SELECTOR, ".gk-otp-fields__input")))

otp = input("Enter the OTP: ")
for i in range(len(otp)):
    otp_fields[i].send_keys(otp[i])
print("PROCESSING.... PLEASE WAIT!!")
# Wait for the form fields to appear
first_name_field = wait.until(EC.presence_of_element_located((By.ID, "input-firstname")))

# Find and fill in the additional form fields
first_name_field = driver.find_element(By.ID, "input-firstname")
first_name_field.send_keys(first_name)
sleep(1)
middle_name_field = driver.find_element(By.ID, "input-middlename")
middle_name_field.send_keys(middle_name)
sleep(1)
last_name_field = driver.find_element(By.ID, "input-lastname")
last_name_field.send_keys(last_name)
sleep(2)

birthdate_field = driver.find_element(By.CSS_SELECTOR,'body > app-root > app-dynamic-home > div > div > app-sim-reg-form > div > div > form > app-sim-reg-form-personal-info > div:nth-child(2) > div > div:nth-child(3) > div')

# Click on the birthdate field to make it active
ActionChains(driver).click(birthdate_field).perform()

# Type the month
ActionChains(driver).send_keys('05').perform()

# Tab to the day field
ActionChains(driver).send_keys(Keys.TAB).perform()

# Type the day
ActionChains(driver).send_keys('16').perform()

# Tab to the year field
ActionChains(driver).send_keys(Keys.TAB).perform()

# Type the year
ActionChains(driver).send_keys('01031998').perform()

# Click outside the field to unfocus it
ActionChains(driver).click(birthdate_field).perform()

# find the dropdown and click on it to open the options
Nationality_dropdown = wait.until(EC.presence_of_element_located((By.ID, "cmb-go_sc-nationality")))
action_chains = ActionChains(driver)
action_chains.double_click(Nationality_dropdown).perform()
Nationality_dropdown.click()
# wait for the options to load
Nationality_options = wait.until(EC.presence_of_all_elements_located((By.CLASS_NAME, "gk-combobox__option-item")))
# select "Philippines" from the options
for option in Nationality_options:
    if option.text == "Philippines":
        option.click()
        break

# find the dropdown and click on it to open the options
gender_dropdown = driver.find_element(By.ID, "cmb-go_sc-gender")
action_chains = ActionChains(driver)
action_chains.double_click(gender_dropdown).perform()
gender_dropdown.click()
# wait for the options to load
sleep(1)
# select "Male" from the options
gender_options = driver.find_elements(By.CLASS_NAME, "gk-combobox__option-item")
for option in gender_options:
    if option.text == "Male":
        option.click()
        break
sleep(2)

height = 1000  # define the height variable
for scrol in range(100, height, 100):
    driver.execute_script(f"window.scrollTo(0,{scrol})")
    sleep(0.1)
# Find and fill in the additional form fields
Unit_field = driver.find_element(By.ID, "base-input3")
Unit_field.send_keys(unit_number)


Street_field = driver.find_element(By.ID, "base-input4")
Street_field.send_keys(street_number)

Unit_field = driver.find_element(By.ID, "base-input5")
Unit_field.send_keys(subd_location)
# Wait for the confirmation page to load
sleep(1)


# Wait for the dropdown to be clickable
dropdown = WebDriverWait(driver, 10).until(
    EC.element_to_be_clickable((By.ID, "cmb-go_sc-province"))
)

action_chains = ActionChains(driver)
action_chains.double_click(dropdown).perform()
dropdown.click()

# Wait for the options to be visible
options = WebDriverWait(driver, 10).until(
    EC.presence_of_all_elements_located((By.CLASS_NAME, "gk-combobox__option-item"))
)

# Find the option with text "cavite" and click it
for option in options:
    if option.text == "CAVITE":
        option.click()
        break

# Input "cavite" into the dropdown
dropdown.send_keys("CAVITE")


                # Wait for the dropdown to be clickable
city_dropdown = WebDriverWait(driver, 10).until(
    EC.element_to_be_clickable((By.ID, "cmb-go_sc-city"))
)

action_chains = ActionChains(driver)
action_chains.double_click(city_dropdown).perform()
city_dropdown.click()
# Wait for the options to be visible
options = WebDriverWait(driver, 10).until(
    EC.presence_of_all_elements_located((By.CLASS_NAME, "gk-combobox__option-item"))
)

# Find the option with text "cavite" and click it
for option in options:
    if option.text == "CITY OF DASMARIÑAS":
        option.click()
        break

# Input "cavite" into the dropdown
city_dropdown.send_keys("CITY OF DASMARIÑAS")


# Wait for the dropdown to be clickable
brgy_dropdown = WebDriverWait(driver, 10).until(
    EC.element_to_be_clickable((By.ID, "cmb-go_sc-brgy"))
)
action_chains = ActionChains(driver)
action_chains.double_click(brgy_dropdown).perform()
brgy_dropdown.click()

# Wait for the options to be visible
options = WebDriverWait(driver, 10).until(
    EC.presence_of_all_elements_located((By.CLASS_NAME, "gk-combobox__option-item"))
)

# Find the option with text "cavite" and click it
for option in options:
    if option.text == "Paliparan I":
        option.click()
        break

# Input "cavite" into the dropdown
brgy_dropdown.send_keys("Paliparan I")
# zipcode
zip_field = driver.find_element(By.ID, "zipcode")
zip_field.send_keys(zipcode)
sleep(1)
# Close the browser

register_button = driver.find_element(By.CSS_SELECTOR, ".go_sc-sim-reg-regform__btn")
register_button.click()
sleep(3)
try:
    next_button = driver.find_element(By.XPATH, '//button[text()="Next"]')
    next_button.click()
except NoSuchElementException:
    pass  # If "Next" button is not found, skip to the next step

print("UPLOADING DOCUMENTS! PLEASE WAIT!!! This is prettyboy...")
sleep(5)
id_type = driver.find_element(By.ID, "cmb-")
action_chains = ActionChains(driver)
action_chains.double_click(id_type).perform()
id_type.click()
# wait for the options to load
sleep(2)
# select "Male" from the options
id_type = driver.find_elements(By.CLASS_NAME, "gk-combobox__option-item")
for option in id_type:
    if option.text == "Others":
        option.click()
        break

zip_field = driver.find_element(By.ID, "base-input1")
zip_field.send_keys(ID_Number)
sleep(3)

file_input = driver.find_element(By.ID, "upload-form-poid0")
# Send the file path to the file input element
file_input.send_keys("/var/UMID.jpg")

file_input = driver.find_element(By.ID, "upload-form-selfie1")
# Send the file path to the file input element
file_input.send_keys("/var/selfie-1.jpg")
sleep(3)
wait = WebDriverWait(driver, 10)
next_button = WebDriverWait(driver, 10).until(EC.element_to_be_clickable((By.XPATH, '//*[@id="go_sc-sim-reg-poid"]/div[4]/button[2]')))
next_button.click()
sleep(10)
# Find the checkbox and check it
checkbox = WebDriverWait(driver, 10).until(EC.element_to_be_clickable((By.CSS_SELECTOR, '#go_sc-sim-reg-tnc > div > div > div.go_sc-scr-tncform__container > div > div.tnc-item-container.ng-star-inserted > label > span')))
checkbox.click()

submit_button = WebDriverWait(driver, 10).until(EC.element_to_be_clickable((By.XPATH, '//*[@id="go_sc-sim-reg-tnc"]/div/div/div[2]/button[2]')))
submit_button.click()

sleep(2)
driver.quit()
print("REGISTRATION SUCCESS. PLEASE WAIT FOR CONFIRMATION...")

