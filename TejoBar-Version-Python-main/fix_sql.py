import os

input_path = r"C:\Users\user\Downloads\tejobar_db (3).sql"
output_path = r"C:\Users\user\Downloads\tejobar_db_fixed.sql"

if not os.path.exists(input_path):
    print(f"Error: {input_path} not found.")
    exit(1)

with open(input_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace collations as requested
# 1. utf8mb4_uca1400_ai_ci -> utf8mb4_unicode_ci
new_content = content.replace('utf8mb4_uca1400_ai_ci', 'utf8mb4_unicode_ci')

# 2. utf8mb4_general_ci -> utf8mb4_unicode_ci (to ensure "all tables use... COLLATE=utf8mb4_unicode_ci")
new_content = new_content.replace('utf8mb4_general_ci', 'utf8mb4_unicode_ci')

# The user also asked to ensure ENGINE=InnoDB and DEFAULT CHARSET=utf8mb4
# Since we already checked and ALL tables used these, and we didn't change them, we are good.
# But let's verify if there were any others just in case.

with open(output_path, 'w', encoding='utf-8') as f:
    f.write(new_content)

print(f"File fixed and saved to: {output_path}")
