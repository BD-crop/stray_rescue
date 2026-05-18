import { useEffect, useState } from 'react'
import { Lock, Mail } from 'lucide-react'
import { Button } from '../components/Button'
import { Card, CardContent, CardHeader } from '../components/Card'

function AuthPage({ mode, onNavigate }) {
  const [isLogin, setIsLogin] = useState(mode === 'login')
  const [selectedRole, setSelectedRole] = useState('')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [error, setError] = useState('')

  useEffect(() => {
    setIsLogin(mode === 'login')
    if (mode === 'login') setSelectedRole('')
  }, [mode])

  const roles = [
    { id: 'user', label: 'Animal Lover', description: 'Report strays and adopt pets' },
    { id: 'volunteer', label: 'Volunteer', description: 'Rescue animals in need' },
    { id: 'shelter', label: 'Shelter Admin', description: 'Manage shelter operations' },
  ]

  const selectedRoleLabel = roles.find((role) => role.id === selectedRole)?.label

  const switchMode = () => {
    const nextLoginState = !isLogin
    setIsLogin(nextLoginState)
    if (nextLoginState) {
      setSelectedRole('')
      onNavigate?.('login')
    } else {
      onNavigate?.('register')
    }
  }

  // ✅ Updated login to use FormData instead of JSON
  const handleLogin = async (event) => {
    event.preventDefault()
    setError('')

    if (!email || !password) {
      setError('All fields are required')
      return
    }

    try {
      // Use FormData to mimic traditional HTML form submission
      const formData = new FormData()
      formData.append('submit', '')
      formData.append('email', email)
      formData.append('password', password)

      let endpoint = 'http://localhost:80/dashboard/login/admin_login.php'
      if (selectedRole === 'user') endpoint = 'http://localhost:80/dashboard/login/user_login.php'
      if (selectedRole === 'volunteer') endpoint = 'http://localhost:80/dashboard/login/volunteer_login.php'

      const response = await fetch(endpoint, {
        method: 'POST',
        body: formData, // ✅ sending as multipart/form-data
        // Do NOT set Content-Type manually! Browser sets it automatically with proper boundary
      })

      // Log for debugging
      console.log('FormData sent:', Array.from(formData.entries()))

      const data = await response.json()

      if (response.ok) {
        console.log('Login success:', data)
        if (data.ssid) localStorage.setItem('ssid', data.ssid)
        onNavigate?.('home')
      } else {
        setError(data.msg || 'Login failed')
      }
    } catch (err) {
      console.error(err)
      setError('An error occurred while logging in.')
    }
  }

  return (
    <div className="relative min-h-screen overflow-hidden px-4 py-10 sm:px-6 lg:px-8">
      <div className="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(249,115,22,0.18),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(251,191,36,0.18),_transparent_32%),linear-gradient(180deg,_#fffaf3_0%,_#ffffff_100%)]" />

      <div className="mx-auto grid max-w-6xl items-center gap-10 lg:grid-cols-[1.05fr_0.95fr]">
        <div className="hidden lg:block">{/* Left-side info panel */}</div>

        <div className="mx-auto w-full max-w-md">
          <Card className="border-orange-100/70 bg-white/92 backdrop-blur-sm">
            <CardHeader>
              {!isLogin && !selectedRole ? (
                <div>
                  <h3 className="text-lg font-black text-slate-900">Choose Your Role</h3>
                  <p className="mt-2 text-sm leading-6 text-slate-500">
                    Select how you&apos;ll contribute so we can tailor your experience.
                  </p>
                  <div className="mt-6 space-y-3">
                    {roles.map((role) => (
                      <button
                        className="w-full rounded-2xl border-2 border-slate-200 bg-white p-4 text-left transition-all hover:border-orange-300 hover:bg-orange-50"
                        key={role.id}
                        onClick={() => setSelectedRole(role.id)}
                        type="button"
                      >
                        <div className="font-black text-slate-900">{role.label}</div>
                        <div className="mt-1 text-sm leading-6 text-slate-500">{role.description}</div>
                      </button>
                    ))}
                  </div>
                </div>
              ) : (
                <div>
                  <div className="mb-6 text-center">
                    <h3 className="text-2xl font-black text-slate-900">
                      {isLogin ? 'Sign In' : `Create ${selectedRoleLabel} Account`}
                    </h3>
                    <p className="mt-2 text-sm leading-6 text-slate-500">
                      {isLogin
                        ? 'Sign in to continue helping animals in need.'
                        : 'Set up your account and join the mission today.'}
                    </p>
                  </div>

                  <form className="space-y-4" onSubmit={handleLogin}>
                    <InputField
                      icon={Mail}
                      label="Email Address"
                      placeholder="you@example.com"
                      type="email"
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                    />
                    <InputField
                      icon={Lock}
                      label="Password"
                      placeholder="••••••••"
                      type="password"
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                    />

                    {error && <div className="text-red-500 text-sm">{error}</div>}

                    {isLogin && (
                      <div className="flex items-center justify-between text-sm">
                        <label className="flex items-center gap-2 font-semibold text-slate-500">
                          <input className="rounded border-slate-300" type="checkbox" />
                          <span>Remember me</span>
                        </label>
                        <button
                          className="font-bold text-orange-500 hover:underline"
                          type="button"
                        >
                          Forgot password?
                        </button>
                      </div>
                    )}

                    <Button className="w-full" size="lg" type="submit">
                      {isLogin ? 'Sign In' : 'Create Account'}
                    </Button>
                  </form>
                </div>
              )}
            </CardHeader>

            {!isLogin && !selectedRole ? null : (
              <CardContent className="pt-0">
                <div className="text-center text-sm">
                  <span className="text-slate-500">
                    {isLogin ? "Don't have an account? " : 'Already have an account? '}
                  </span>
                  <button
                    className="font-black text-orange-500 hover:underline"
                    onClick={switchMode}
                    type="button"
                  >
                    {isLogin ? 'Sign up' : 'Sign in'}
                  </button>
                </div>
              </CardContent>
            )}
          </Card>

          <div className="mt-6 text-center text-sm leading-6 text-slate-500">
            By continuing, you agree to our Terms of Service and Privacy Policy
          </div>
        </div>
      </div>
    </div>
  )
}

function InputField({ icon: Icon, label, type, placeholder, value, onChange }) {
  return (
    <div>
      <label className="mb-2 block text-sm font-bold text-slate-700">{label}</label>
      <div className="relative">
        <Icon className="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
        <input
          className="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-slate-900 outline-none transition focus:border-orange-300 focus:bg-white focus:ring-4 focus:ring-orange-100"
          placeholder={placeholder}
          type={type}
          value={value}
          onChange={onChange}
        />
      </div>
    </div>
  )
}

export default AuthPage