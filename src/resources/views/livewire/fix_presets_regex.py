
import re

file_path = '/var/www/beartropy/tables/src/resources/views/livewire/table-presets.php'

# Colored themes list
# Check which ones are colored.
colored_themes = ['red', 'orange', 'amber', 'yellow', 'lime', 'green', 'emerald', 'teal', 'cyan', 'sky', 'blue', 'indigo', 'violet', 'purple', 'fuchsia', 'pink', 'rose']

# Neutral themes list
neutral_themes = ['beartropy', 'slate', 'gray', 'zinc', 'neutral', 'stone']

with open(file_path, 'r') as f:
    content = f.read()

# 1. Update Colored Themes (Force 300/600 and add 'border ')
for color in colored_themes:
    # We want to replace matching line in 'dropdowns' key.
    # Pattern: 'border' => '...'
    # But filtering by color name in the key is hard.
    # However, the structure is:
    # 'color' => [ ... 'dropdowns' => [ ... 'border' => '...' ] ]
    
    # We can use a regex that matches the structure or we can just find-replace lines that contain the color in the value.
    # Most likely, for 'lime' theme, the border definition contains 'lime'.
    
    # Regex to capture: 'border' => '[anything including lime] [anything including dark:border-something]',
    
    # We want to enforce: 'border' => 'border border-{color}-300 dark:border-{color}-600',
    
    expected_line = f"'border' => 'border border-{color}-300 dark:border-{color}-600',"
    
    # Regex pattern:
    # 'border' =>\s*['"](?:border\s+)?(border-)?{color}-\d+\s+dark:border-(?:{color}|gray)-\d+['"],
    # We need to match variations:
    # border-{color}-200
    # border-{color}-300
    # border border-{color}-300
    # dark:border-gray-600
    # dark:border-gray-700
    # dark:border-{color}-600
    
    # Let's match line content:
    # \s*'border'\s*=>\s*['"].*?{color}.*?['"],
    
    # Safest way: Iteratively replace known bad patterns for specific color.
    # Or just Regex replace any line that looks like a border definition for this color.
    
    regex = r"'border'\s*=>\s*['\"](?:border\s+)?border-" + color + r"-\d+\s+dark:border-(?:" + color + r"|gray)-\d+['\"],"
    
    # We verify if this regex matches lines like:
    # 'border' => 'border-lime-200 dark:border-gray-600',
    # 'border' => 'border-lime-200 dark:border-gray-700',
    
    content = re.sub(regex, expected_line, content)


# 2. Update Neutral Themes (Prepend 'border ' ONLY if not present, keep existing color/shade)
# For neutral themes, we trust the color is correct (e.g. gray, slate, etc.) but we need to ensure 'border' width class is there.
# And we must NOT change the shades (user said beartropy is correct).

for nc in neutral_themes:
    # For 'beartropy', color is 'gray'.
    # We should search for lines that look like border definitions using these colors.
    
    current_color = 'gray' if nc == 'beartropy' else nc
    
    # Regex: 'border' => '(border-current_color-\d+ dark:border-current_color-\d+)',
    # Capture group 1 is the value without 'border ' prefix.
    # Negative lookbehind to ensure it doesn't already have 'border '.
    
    # Actually, simpler:
    # Find: 'border' => 'border-[color]-...
    # Replace: 'border' => 'border border-[color]-...
    
    # But only if it doesnt have 'border border-'.
    
    regex = r"('border'\s*=>\s*['\"])(border-" + current_color + r"-\d+\s+dark:border-" + current_color + r"-\d+['\"],)"
    
    content = re.sub(regex, r"\1border \2", content)


with open(file_path, 'w') as f:
    f.write(content)

print("Updated table-presets.php with robust regex")
