import { AlertCircle, Camera, CheckCircle, MapPin, Navigation, Phone, Upload } from 'lucide-react'
import { Button } from '../components/Button'
import { Card, CardContent, CardHeader } from '../components/Card'

function ReportAnimalPage() {
  return (
    <div className="min-h-screen px-4 py-12 sm:px-6 lg:px-8">
      <div className="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[0.85fr_1.15fr]">
        <section className="rounded-[34px] bg-[linear-gradient(150deg,_#f97316_0%,_#fbbf24_100%)] p-8 text-white shadow-[0_28px_70px_rgba(249,115,22,0.28)] lg:p-10">
          <div className="mb-5 inline-flex rounded-full bg-white/18 px-4 py-2 text-xs font-black uppercase tracking-[0.22em]">
            Emergency report
          </div>
          <h1 className="text-5xl font-black leading-none tracking-tight">Help starts with a precise report.</h1>
          <p className="mt-5 text-lg leading-8 text-white/90">
            Share the animal condition, location, and contact details. This frontend keeps the form
            ready for your backend submission flow.
          </p>
          <div className="mt-8 grid gap-4">
            {[
              ['Location accuracy', 'Pin the nearest landmark for volunteer dispatch.'],
              ['Animal condition', 'Tell rescuers whether urgent medical care is needed.'],
              ['Photo evidence', 'Upload clear images so shelters can prepare.'],
            ].map(([title, copy]) => (
              <div className="rounded-3xl border border-white/18 bg-white/12 p-5" key={title}>
                <CheckCircle className="mb-3 h-6 w-6" />
                <div className="font-black">{title}</div>
                <p className="mt-1 text-sm leading-6 text-white/80">{copy}</p>
              </div>
            ))}
          </div>
        </section>

        <Card className="border-orange-100/70 bg-white/95">
          <CardHeader>
            <h2 className="text-3xl font-black text-slate-900">Report Stray Animal</h2>
            <p className="mt-2 text-slate-500">Fields are UI-only for now and ready for API wiring.</p>
          </CardHeader>
          <CardContent>
            <form className="grid gap-5" onSubmit={(event) => event.preventDefault()}>
              <div className="grid gap-5 md:grid-cols-2">
                <Field icon={MapPin} label="Location" placeholder="Road 12, Dhanmondi" />
                <Field icon={Phone} label="Contact Number" placeholder="+880 1234-567890" />
              </div>
              <div className="grid gap-5 md:grid-cols-2">
                <Select label="Animal Type" options={['Dog', 'Cat', 'Bird', 'Other']} />
                <Select label="Condition" options={['Injured', 'Sick', 'Healthy but lost', 'Mother with babies']} />
              </div>
              <label>
                <span className="mb-2 block text-sm font-bold text-slate-700">Description</span>
                <textarea className="min-h-36 w-full rounded-2xl border border-slate-200 bg-slate-50 p-4 outline-none focus:border-orange-300 focus:bg-white focus:ring-4 focus:ring-orange-100" placeholder="Describe what you saw, animal behavior, nearby risks, and best access point." />
              </label>
              <div className="rounded-[26px] border-2 border-dashed border-orange-200 bg-orange-50/60 p-8 text-center">
                <Camera className="mx-auto h-10 w-10 text-orange-500" />
                <div className="mt-3 font-black text-slate-900">Upload rescue photos</div>
                <p className="mt-1 text-sm text-slate-500">PNG or JPG preview area for your backend upload handler.</p>
                <Button className="mt-5" icon={<Upload className="h-5 w-5" />} variant="outline">Choose Files</Button>
              </div>
              <div className="flex flex-wrap gap-4">
                <Button icon={<AlertCircle className="h-5 w-5" />} size="lg" type="submit">
                  Submit Report
                </Button>
                <Button icon={<Navigation className="h-5 w-5" />} size="lg" variant="outline">
                  Use Current Location
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}

function Field({ icon: Icon, label, placeholder }) {
  return (
    <label>
      <span className="mb-2 block text-sm font-bold text-slate-700">{label}</span>
      <span className="relative block">
        <Icon className="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
        <input className="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-4 outline-none focus:border-orange-300 focus:bg-white focus:ring-4 focus:ring-orange-100" placeholder={placeholder} />
      </span>
    </label>
  )
}

function Select({ label, options }) {
  return (
    <label>
      <span className="mb-2 block text-sm font-bold text-slate-700">{label}</span>
      <select className="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 font-semibold outline-none focus:border-orange-300 focus:bg-white focus:ring-4 focus:ring-orange-100">
        {options.map((option) => <option key={option}>{option}</option>)}
      </select>
    </label>
  )
}

export default ReportAnimalPage
