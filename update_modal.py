import os

with open(r'c:\Users\SERVER SANS\SANS-PROJECT\sans-sd\modal.txt', 'r', encoding='utf-16') as f:
    hrd_modal = f.read()

hrd_modal = "        <!-- MODAL DETAIL PEGAWAI (ADAPTED FROM HRD) -->\n        <div x-data><template x-teleport=\"body\">\n" + hrd_modal + "\n        </template></div>"

projects = [
    r'c:\Users\SERVER SANS\SANS-PROJECT\sans-sd',
    r'c:\Users\SERVER SANS\SANS-PROJECT\sans-smp',
    r'c:\Users\SERVER SANS\SANS-PROJECT\sans-paud'
]

for proj in projects:
    filepath = os.path.join(proj, r'resources\views\admin\employees\index.blade.php')
    if os.path.exists(filepath):
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        start_marker = "<!-- MODAL DETAIL PEGAWAI"
        end_marker = "@include('admin.employees.modals')"
        
        start_idx = content.find(start_marker)
        end_idx = content.find(end_marker)
        
        if start_idx != -1 and end_idx != -1:
            new_content = content[:start_idx] + hrd_modal + "\n        " + content[end_idx:]
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print("Updated " + filepath)
        else:
            print("Markers not found in " + filepath)
    else:
        print("File not found: " + filepath)
