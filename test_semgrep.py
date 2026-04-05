import subprocess

def run_semgrep():
    result = subprocess.run(
        ["semgrep", "--config", "rules.yaml", "test_code/"],
        capture_output=True,
        text=True
    )

    print("=== SEMGREP OUTPUT ===")
    print(result.stdout)

if __name__ == "__main__":
    run_semgrep()