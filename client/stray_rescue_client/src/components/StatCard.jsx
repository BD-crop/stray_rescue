function StatCard({ icon: Icon, value, label, iconColor = 'text-orange-500' }) {
  return (
    <div className="group rounded-[26px] border border-slate-200/80 bg-white p-6 shadow-[0_18px_40px_rgba(15,23,42,0.05)] transition-transform duration-300 hover:-translate-y-1.5">
      <div className="flex items-center gap-4">
        <div className="grid h-14 w-14 place-items-center rounded-2xl bg-orange-50 ring-1 ring-orange-100">
          <Icon className={`h-6 w-6 ${iconColor}`} />
        </div>
        <div>
          <div className="text-3xl font-black tracking-tight text-slate-900">{value}</div>
          <div className="text-sm font-semibold text-slate-500">{label}</div>
        </div>
      </div>
    </div>
  )
}

export { StatCard }
