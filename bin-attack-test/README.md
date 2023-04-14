# Bin Attack Tests

https://user-images.githubusercontent.com/2927894/232045636-d79109b3-9e7d-45c4-83e0-ebe2350e8d7f.mp4

---

The Tunl Embedded Form was tested for vulnerability to bin attacks.  

This exploit script (not included for obvious reasons) generates the following unique values for every test:

- first and last names
- valid credit card numbers (luhn algo)
- valid expire date
- cvv
- street
- zip

Generating these unique values simulates an actual bin attack where the attacker is likely to use a list of suspected cards and info that are all each unique.

This test is generating valid values that will pass the front end (browser) form validation rules.

The goal is to see if captcha blocks the scripted attempt to get a positive or negative results from our actual Tunl API, nothing else.

The test expects `Captcha Failed.` response messages.  If this is true, the test "passes" meaning it successfully blocked the bot script.

Any other response is interpreted as a potential failure.  Failures due to request "timeouts" are not considered failures.

#### This test was ran 100 times each on 5 different browsers for a total of 500 tests.  The total time for the attack took 14 minutes. Which is a little longer the 1.5 seconds each.

### [Click here to see the full test results](https://ckc-technologies.github.io/tunl-embedded-payment-form/bin-attack-report.html)

Sample Report Screenshot:

![image](https://user-images.githubusercontent.com/2927894/232043524-c2ecd70e-6251-4e0c-93ca-dc6fd29eaf55.png)
