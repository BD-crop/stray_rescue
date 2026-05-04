import { useEffect, useState } from 'react'
import {
  Earth,
  BadgeCheck,
  Heart,
  Lock,
  Mail,
  MapPin,
  Phone,
  User,
} from 'lucide-react'
import { Button } from '../components/Button'
import { Card, CardContent, CardHeader } from '../components/Card'

function AuthPage({ mode, onNavigate }) {
  const [isLogin, setIsLogin] = useState(mode === 'login')
  const [selectedRole, setSelectedRole] = useState('')

  useEffect(() => {
    setIsLogin(mode === 'login')
    if (mode === 'login') {
      setSelectedRole('')
    }
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

  return (
    <div className="relative min-h-screen overflow-hidden px-4 py-10 sm:px-6 lg:px-8">
      <div className="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(249,115,22,0.18),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(251,191,36,0.18),_transparent_32%),linear-gradient(180deg,_#fffaf3_0%,_#ffffff_100%)]" />

      <div className="mx-auto grid max-w-6xl items-center gap-10 lg:grid-cols-[1.05fr_0.95fr]">
        <div className="hidden lg:block">
          <div className="max-w-xl">
            <div className="mb-6 inline-flex items-center gap-3 rounded-full border border-orange-100 bg-white px-5 py-3 shadow-sm">
              <span className="grid h-10 w-10 place-items-center rounded-full bg-orange-100 text-orange-500">
                <Heart className="h-5 w-5 fill-current" />
              </span>
              <span className="text-sm font-black uppercase tracking-[0.18em] text-slate-700">
                Rescue. Recover. Rehome.
              </span>
            </div>

            <h1 className="text-5xl font-black leading-[0.95] tracking-[-0.06em] text-slate-900">
              {isLogin ? 'Welcome back to the rescue network.' : 'Create your place in the mission.'}
            </h1>

            <p className="mt-6 text-lg leading-8 text-slate-500">
              {isLogin
                ? 'Pick up where you left off, coordinate rescue updates, and continue helping strays find safety.'
                : 'Join rescuers, adopters, and shelter teams who are changing animal welfare across Bangladesh every day.'}
            </p>

            <div className="mt-10 grid gap-4">
              {[
                'Fast rescue reporting and volunteer dispatch',
                'Verified shelter network and adoption support',
                'Role-based tools for communities, rescuers, and admins',
              ].map((item) => (
                <div className="flex items-center gap-3" key={item}>
                  <span className="grid h-10 w-10 place-items-center rounded-2xl bg-emerald-50 text-emerald-500">
                    <Heart className="h-4 w-4 fill-current" />
                  </span>
                  <span className="text-base font-semibold text-slate-600">{item}</span>
                </div>
              ))}
            </div>
          </div>
        </div>

        <div className="mx-auto w-full max-w-md">
          <div className="mb-8 text-center lg:hidden">
            <div className="mb-4 flex items-center justify-center gap-2">
              <div className="rounded-full bg-orange-500 p-3 text-white shadow-lg">
                <Heart className="h-8 w-8 fill-current" />
              </div>
            </div>
            <h1 className="text-3xl font-black text-slate-900">
              {isLogin ? 'Welcome Back' : 'Join StrayRescue'}
            </h1>
            <p className="mt-2 text-slate-500">
              {isLogin ? 'Continue your mission to save lives' : 'Start making a difference today'}
            </p>
          </div>

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

                  <div className="mb-6 flex gap-3">
                    <button
                      className="flex flex-1 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 font-bold text-slate-700 transition-colors hover:bg-orange-50"
                      type="button"
                    >
                      <Earth className="h-5 w-5" />
                      <span className="text-sm">Google</span>
                    </button>
                    <button
                      className="flex flex-1 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 font-bold text-slate-700 transition-colors hover:bg-orange-50"
                      type="button"
                    >
                      <BadgeCheck className="h-5 w-5" />
                      <span className="text-sm">Facebook</span>
                    </button>
                  </div>

                  <div className="relative mb-6">
                    <div className="absolute inset-0 flex items-center">
                      <div className="w-full border-t border-slate-200" />
                    </div>
                    <div className="relative flex justify-center text-sm">
                      <span className="bg-white px-3 font-semibold text-slate-400">
                        Or continue with email
                      </span>
                    </div>
                  </div>

                  <form className="space-y-4" onSubmit={(event) => event.preventDefault()}>
                    {!isLogin ? (
                      <InputField
                        icon={User}
                        label="Full Name"
                        placeholder="John Doe"
                        type="text"
                      />
                    ) : null}

                    <InputField
                      icon={Mail}
                      label="Email Address"
                      placeholder="you@example.com"
                      type="email"
                    />

                    {!isLogin ? (
                      <>
                        <InputField
                          icon={Phone}
                          label="Phone Number"
                          placeholder="+880 1234-567890"
                          type="tel"
                        />
                        <InputField
                          icon={MapPin}
                          label="Location"
                          placeholder="Dhaka, Bangladesh"
                          type="text"
                        />
                      </>
                    ) : null}

                    <InputField
                      icon={Lock}
                      label="Password"
                      placeholder="••••••••"
                      type="password"
                    />

                    {isLogin ? (
                      <div className="flex items-center justify-between text-sm">
                        <label className="flex items-center gap-2 font-semibold text-slate-500">
                          <input className="rounded border-slate-300" type="checkbox" />
                          <span>Remember me</span>
                        </label>
                        <button className="font-bold text-orange-500 hover:underline" type="button">
                          Forgot password?
                        </button>
                      </div>
                    ) : null}

                    <Button
                      className="w-full"
                      onClick={() => onNavigate?.('home')}
                      size="lg"
                      type="submit"
                    >
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

function InputField({ icon: Icon, label, type, placeholder }) {
  return (
    <div>
      <label className="mb-2 block text-sm font-bold text-slate-700">{label}</label>
      <div className="relative">
        <Icon className="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
        <input
          className="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-slate-900 outline-none transition focus:border-orange-300 focus:bg-white focus:ring-4 focus:ring-orange-100"
          placeholder={placeholder}
          type={type}
        />
      </div>
    </div>
  )
}

export default AuthPage
