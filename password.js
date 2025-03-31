function checkPasswordStrength() {
  const password = document.getElementById("password").value;
  const strengthText = document.getElementById("password-strength");
  const missingText = document.getElementById("password-missing");

  // Define regex patterns for different types of characters
  const patterns = {
    lowercase: /[a-z]/,
    uppercase: /[A-Z]/,
    numbers: /[0-9]/,
    symbols: /[^a-zA-Z0-9]/
  };

  // Define an object to store the count of different types of characters
  const counts = {
    lowercase: 0,
    uppercase: 0,
    numbers: 0,
    symbols: 0
  };

  // Loop through each character in the password
  for (let i = 0; i < password.length; i++) {
    const char = password.charAt(i);

    // Check if the character matches any of the regex patterns
    for (let key in patterns) {
      if (patterns[key].test(char)) {
        counts[key]++;
      }
    }
  }

  // Calculate the total number of characters in the password
  const totalCount = Object.values(counts).reduce((a, b) => a + b);

  // Calculate the complexity of the password based on the counts of different types of characters
  let complexity = 0;
  for (let key in counts) {
    if (counts[key] > 0) {
      complexity += 1;
    }
  }

  let strengthTextValue = "";
  let missingTextValue = "";

  switch (complexity) {
    case 0:
      strengthTextValue = "";
      missingTextValue = "Your password is missing: at least one lowercase and one uppercase letter, one number, and one symbol.";
      break;
    case 1:
      strengthTextValue = "Weak";
      missingTextValue = "Your password is missing: ";
      if (counts.lowercase === 0 && counts.uppercase === 0) {
        missingTextValue += "at least one lowercase and one uppercase letter, ";
      }
      if (counts.numbers === 0) {
        missingTextValue += "at least one number, ";
      }
      if (counts.symbols === 0) {
        missingTextValue += "at least one symbol, ";
      }
      missingTextValue = missingTextValue.slice(0, -2) + ".";
      break;
    case 2:
      strengthTextValue = "Fair";
      missingTextValue = "Your password is missing: ";
      if (counts.lowercase === 0 || counts.uppercase === 0) {
        missingTextValue += "both lowercase and uppercase letters, ";
      }
      if (counts.numbers === 0 || counts.symbols === 0) {
        missingTextValue += "both numbers and symbols, ";
      }
      missingTextValue = missingTextValue.slice(0, -2) + ".";
      break;
    case 3:
      strengthTextValue = "Good";
      missingTextValue = "Your password is missing: ";
      if (counts.lowercase === 0 || counts.uppercase === 0) {
        missingTextValue += "both lowercase and uppercase letters, ";
      }
      if (counts.numbers === 0) {
        missingTextValue += "at least one number, ";
      }
      if (counts.symbols === 0) {
        missingTextValue += "at least one symbol, ";
      }
      if (password.length < 12) {
        missingTextValue += "at least 12 characters, ";
      }
      missingTextValue = missingTextValue.slice(0, -2) + ".";
      break;
    case 4:
      strengthTextValue = "Strong";
      missingTextValue = "";
      break;

    default:
      strengthTextValue = "";
      missingTextValue = "";
      break;
  }

  // Update the text in the HTML
  strengthText.innerText = strengthTextValue;
  missingText.innerText = missingTextValue;
}
