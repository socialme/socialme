import hmac
import os
import random

def passhash(password):
	# returns a hashed hmac with a authenicated seed
	return hmac.new(_userSecret(), password, "sha256").hexdigest()

def _userSecret():
	try:
		secret = file('.user-sec').read()
	except IOError:
		secret = os.urandom(random.randint(20, 40))
		file('.user-sec').write(secret)

	return secret


