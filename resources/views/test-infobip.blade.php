@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Test Infobip WhatsApp Integration</h4>
                    <small class="text-muted">Use this page to test WhatsApp message sending</small>
                </div>
                <div class="card-body">
                    <form id="testForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="phone">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   placeholder="e.g., 263718091130 or +263718091130" required>
                            <small class="form-text text-muted">
                                Enter phone number in any format (263..., 0..., +263...). 
                                It will be automatically normalized to E.164 format.
                            </small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="message">Test Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" 
                                      placeholder="Enter your test message here..." required></textarea>
                            <small class="form-text text-muted">Maximum 1000 characters</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="sendBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            Send Test Message
                        </button>
                    </form>
                    
                    <div id="result" class="mt-4"></div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5>Testing Instructions</h5>
                </div>
                <div class="card-body">
                    <h6>1. Direct Message Testing</h6>
                    <p>Use the form above to send a test message directly to any phone number.</p>
                    
                    <h6>2. Fault Lifecycle Testing</h6>
                    <p>To test automatic notifications during fault lifecycle:</p>
                    <ul>
                        <li><strong>Fault Creation:</strong> Create a new fault - NOC technicians should receive notifications</li>
                        <li><strong>Fault Assessment:</strong> Assess a fault - Chief technicians in the fault's region should receive notifications</li>
                        <li><strong>Fault Assignment:</strong> Assign a fault to a technician - The assigned technician should receive notifications</li>
                        <li><strong>Status Updates:</strong> Update fault status - The assigned technician should receive progress notifications</li>
                    </ul>
                    
                    <h6>3. Check Application Logs</h6>
                    <p>Monitor <code>storage/logs/laravel.log</code> for Infobip API responses and any errors.</p>
                    
                    <h6>4. Verify Recipients</h6>
                    <p>Ensure users have valid phone numbers in the database and belong to the correct departments/sections.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('testForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const sendBtn = document.getElementById('sendBtn');
    const spinner = sendBtn.querySelector('.spinner-border');
    const resultDiv = document.getElementById('result');
    
    // Show loading state
    sendBtn.disabled = true;
    spinner.classList.remove('d-none');
    resultDiv.innerHTML = '';
    
    try {
        const formData = new FormData(this);
        const response = await fetch('{{ route("test.infobip.send") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const contentType = response.headers.get('content-type') || '';
        let data;
        if (contentType.includes('application/json')) {
            data = await response.json();
        } else {
            const text = await response.text();
            throw new Error(`Non-JSON response (status ${response.status}): ${text.substring(0, 200)}...`);
        }
        
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <h6>✅ Message Sent Successfully!</h6>
                    <p>${data.message}</p>
                    <details>
                        <summary>API Response Details</summary>
                        <pre class="mt-2">${JSON.stringify(data.result, null, 2)}</pre>
                    </details>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h6>❌ Failed to Send Message</h6>
                    <p>${data.message}</p>
                </div>
            `;
        }
    } catch (error) {
        resultDiv.innerHTML = `
            <div class="alert alert-danger">
                <h6>❌ Network Error</h6>
                <p>Failed to send request: ${error.message}</p>
            </div>
        `;
    } finally {
        // Hide loading state
        sendBtn.disabled = false;
        spinner.classList.add('d-none');
    }
});
</script>
@endsection