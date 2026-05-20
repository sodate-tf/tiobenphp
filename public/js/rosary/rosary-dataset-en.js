(function () {
  window.ROSARY_DATASET = window.ROSARY_DATASET || {};

  const PRAYERS = {
    openingBundle: {
      title: "Opening Prayers (Sign of the Cross + Creed + Offering)",
      text: `The Sign of the Cross
In the name of the Father, and of the Son, and of the Holy Spirit. Amen.

The Apostles' Creed
I believe in God, the Father almighty, Creator of heaven and earth, and in Jesus Christ, his only Son, our Lord, who was conceived by the Holy Spirit, born of the Virgin Mary, suffered under Pontius Pilate, was crucified, died and was buried; he descended into hell; on the third day he rose again from the dead; he ascended into heaven, and is seated at the right hand of God the Father almighty; from there he will come to judge the living and the dead. I believe in the Holy Spirit, the holy catholic Church, the communion of saints, the forgiveness of sins, the resurrection of the body, and life everlasting. Amen.

The Offering of the Rosary
Divine Jesus, we offer this Rosary, which we are about to pray, meditating on the mysteries of our Redemption. Grant us, through the intercession of the Virgin Mary, Mother of God and our Mother, the virtues necessary to pray it well and the grace to gain the indulgences of this holy devotion. Amen.`
    },

    ourFather: {
      title: "Our Father",
      text: `Our Father, who art in heaven, hallowed be thy name; thy kingdom come, thy will be done on earth as it is in heaven. Give us this day our daily bread, and forgive us our trespasses, as we forgive those who trespass against us; and lead us not into temptation, but deliver us from evil. Amen.`
    },

    hailMary: {
      title: "Hail Mary",
      text: `Hail, Mary, full of grace, the Lord is with thee. Blessed art thou among women, and blessed is the fruit of thy womb, Jesus. Holy Mary, Mother of God, pray for us sinners, now and at the hour of our death. Amen.`
    },

    gloryFatima: {
      title: "Glory Be + Fatima Prayer",
      text: `Glory be to the Father, and to the Son, and to the Holy Spirit. As it was in the beginning, is now, and ever shall be, world without end. Amen.

O my Jesus, forgive us our sins, save us from the fires of hell; lead all souls to Heaven, especially those in most need of Thy mercy.`
    },

    hailHolyQueen: {
      title: "Hail, Holy Queen",
      text: `Hail, Holy Queen, Mother of Mercy, our life, our sweetness and our hope. To thee do we cry, poor banished children of Eve; to thee do we send up our sighs, mourning and weeping in this valley of tears. Turn then, most gracious advocate, thine eyes of mercy toward us, and after this our exile, show unto us the blessed fruit of thy womb, Jesus. O clement, O loving, O sweet Virgin Mary.`
    },

    finalPrayer: {
      title: "Concluding Prayer",
      text: `Pray for us, O holy Mother of God, that we may be made worthy of the promises of Christ.

Let us pray:
O God, whose only begotten Son, by His life, death, and resurrection, has purchased for us the rewards of eternal salvation, grant, we beseech Thee, that meditating upon these mysteries of the Most Holy Rosary of the Blessed Virgin Mary, we may imitate what they contain and obtain what they promise, through the same Christ our Lord. Amen.`
    },
  };

  const OPENING_HAIL_MARY_MEDITATIONS = {
    faith: {
      title: "1st Hail Mary — For Faith",
      short: "Pray for a living faith: to trust in God even when the full path is not yet visible.",
      long: `In this first Hail Mary, ask for the grace to believe as Mary believed: a faith that welcomes God's will before fully understanding it. Faith is not merely "believing God exists," but handing Him the keys to your heart and your choices. It is trusting His Word when feelings waver and when answers seem delayed.

While praying, bring to mind a specific area where you need to trust again: a difficult choice, a recurring fear, or a life stage that feels uncertain. 
Then, surrender it simply: "Lord, I believe. Help my unbelief."

To contemplate:
- In what areas have I been trying to control everything on my own?
- Where is God asking for my "Yes" today?

Grace to ask for:
"Jesus, make my faith obedient and steadfast."`,
      scriptures: [{ ref: "Lk 1:38" }, { ref: "Mk 9:24" }, { ref: "Heb 11:1" }],
    },

    hope: {
      title: "2nd Hail Mary — For Hope",
      short: "Pray for a firm hope: to rely on God's promises without losing heart.",
      long: `In this second Hail Mary, ask for hope: the virtue that sustains the soul when the present seems to contradict God's promises. Hope is not naive optimism. It is the certainty that God does not fail—and that He guides you even when you feel lost.

Hope heals the inner discouragement that whispers "it won't change" or "I can't do this."
While praying, present to God what tires you: a constant worry, a family issue, or a wound that won't heal.
Ask for the grace to persevere: to keep doing good and maintaining your dignity without hardening your heart.

To contemplate:
- Am I giving up on the inside?
- Have I let a current pain become a permanent sentence?

Grace to ask for:
"Lord, strengthen my hope and give me constancy."`,
      scriptures: [{ ref: "Rom 5:3-5" }, { ref: "Ps 27:14" }, { ref: "Lam 3:21-23" }],
    },

    charity: {
      title: "3rd Hail Mary — For Charity",
      short: "Pray for an ardent charity: to love with patience, truth, and concrete self-giving.",
      long: `In this third Hail Mary, ask for charity: the love that comes from God and changes how you treat others. Charity is more than just "being polite." It is to love truly: to forgive, to serve, to bear with patience, and to desire the real good of the other—even when it costs you.

Charity heals egoism (making everything about me) and resentment (living in reaction to the harm I've received).
While praying, choose someone to place in your heart: a loved one, a difficult person, or someone you've been avoiding.
Ask for the grace to love through actions: "Lord, teach me to love without measure."

To contemplate:
- Do I do good with joy or out of obligation?
- Is there someone I need to forgive in a concrete way?

Grace to ask for:
"Jesus, give me a heart that is meek and available."`,
      scriptures: [{ ref: "1 Cor 13:4-7" }, { ref: "Jn 13:34-35" }, { ref: "Col 3:12-14" }],
    },
  };

  const MYSTERIES = {
    gozosos: {
      label: "Joyful Mysteries (Monday & Saturday)",
      themeHint: "joy, humility, welcoming, obedience, the hidden life",
      items: [
        {
          index: 1,
          title: "The Annunciation",
          shortReflection: "Contemplate Mary's 'Yes': a faith that welcomes God's will.",
          longReflection: "Faced with the Angel's announcement, Mary does not close herself in fear. She listens, asks with humility, and trusts. This mystery teaches us that God's will is not a burden, but a path to life.",
          intention: "Pray for the grace to say 'Yes' to God with promptness and faith.",
          scriptures: [{ ref: "Lk 1:26-38" }, { ref: "Is 7:14" }],
        },
        {
          index: 2,
          title: "The Visitation",
          shortReflection: "Charity is in a hurry: Mary brings Christ and joy visits a home.",
          longReflection: "Mary does not keep the gift for herself. She sets out on a journey and brings joy to Elizabeth. This mystery forms a faith that becomes service.",
          intention: "Pray for a heart attentive to the needs of others and the courage to serve.",
          scriptures: [{ ref: "Lk 1:39-56" }, { ref: "Rom 12:10-13" }],
        },
        {
          index: 3,
          title: "The Nativity",
          shortReflection: "God chooses simplicity: the manger reveals a love that approaches without imposing.",
          longReflection: "Christ is born poor so that no one is afraid to draw near. The Savior enters history through the path of humility.",
          intention: "Pray for humility and a spirit of poverty to recognize God in simple things.",
          scriptures: [{ ref: "Lk 2:1-20" }, { ref: "Jn 1:14" }],
        },
        {
          index: 4,
          title: "The Presentation in the Temple",
          shortReflection: "Giving back to God what belongs to God: life as an offering.",
          longReflection: "Mary and Joseph present Jesus in the Temple: a gesture of obedience. Simeon recognizes salvation and foretells that true love also passes through pain.",
          intention: "Pray for fidelity and constancy in prayer and mission, even during trials.",
          scriptures: [{ ref: "Lk 2:22-35" }, { ref: "Ps 116:12-14" }],
        },
        {
          index: 5,
          title: "The Finding of Jesus in the Temple",
          shortReflection: "When Jesus feels distant, look for Him in the right place: in His Father's house.",
          longReflection: "Finding Jesus in the Temple teaches us that faith does not depend on feelings. When silence comes, we continue seeking until we find Him.",
          intention: "Pray for perseverance when faith goes through silence and for the trust to return to the essentials.",
          scriptures: [{ ref: "Lk 2:41-52" }, { ref: "Prov 3:5-6" }],
        },
      ],
    },

    dolorosos: {
      label: "Sorrowful Mysteries (Tuesday & Friday)",
      themeHint: "compassion, conversion, surrender, patience, redeeming love",
      items: [
        {
          index: 1,
          title: "The Agony in the Garden",
          shortReflection: "Christ faces fear through prayer: loving obedience overcomes the urge to flee.",
          longReflection: "In Gethsemane, Jesus feels the weight of the world's sin, but He does not run. Prayer doesn't remove the cross, but transforms the heart to carry it.",
          intention: "Pray for strength in your own difficult nights and the courage to trust God's will.",
          scriptures: [{ ref: "Mt 26:36-46" }, { ref: "Heb 5:7-9" }],
        },
        {
          index: 2,
          title: "The Scourging at the Pillar",
          shortReflection: "Love endures violence to heal our wounds: Christ offers Himself for us.",
          longReflection: "The scourging reveals the gravity of sin and the depth of mercy. Jesus does not return hate; He redeems it through His suffering.",
          intention: "Pray for liberation from vices and offer reparation for the sins of the world.",
          scriptures: [{ ref: "Jn 19:1" }, { ref: "Is 53:4-5" }],
        },
        {
          index: 3,
          title: "The Crowning with Thorns",
          shortReflection: "The world mocks, but Christ reigns with meekness: true glory is loving to the end.",
          longReflection: "Humiliated and ridiculed, Jesus remains meek. This mystery heals our vanity and our constant need for approval.",
          intention: "Pray for humility and the grace not to respond with aggression when wounded.",
          scriptures: [{ ref: "Mt 27:27-31" }, { ref: "Phil 2:5-11" }],
        },
        {
          index: 4,
          title: "The Carrying of the Cross",
          shortReflection: "The cross is not defeat: it is a path of love, with falls and new beginnings.",
          longReflection: "On the way to Calvary, Christ shows that to save is to carry. He accepts help, falls, and gets back up.",
          intention: "Pray for patience and constancy to bear setbacks without losing heart.",
          scriptures: [{ ref: "Lk 23:26-32" }, { ref: "Mt 16:24-25" }],
        },
        {
          index: 5,
          title: "The Crucifixion and Death of Jesus",
          shortReflection: "On the cross, God loves us without measure: new life is born in forgiveness.",
          longReflection: "From the cross, Jesus forgives and entrusts Mary to the Church. This is the school of mercy: forgiving and loving even when it costs everything.",
          intention: "Pray for reconciliation and offer your life for the peace and salvation of souls.",
          scriptures: [{ ref: "Lk 23:33-46" }, { ref: "Jn 19:25-30" }],
        },
      ],
    },

    gloriosos: {
      label: "Glorious Mysteries (Wednesday & Sunday)",
      themeHint: "hope, victory over sin, new life, the Holy Spirit",
      items: [
        {
          index: 1,
          title: "The Resurrection",
          shortReflection: "Life wins: hope is born from the encounter with the Risen Lord.",
          longReflection: "The Resurrection is the foundation of our faith. It cures despair and reminds us that God always has the final word over death.",
          intention: "Pray for Paschal joy and a firm faith that is not dominated by fear or discouragement.",
          scriptures: [{ ref: "Mt 28:1-10" }, { ref: "1 Cor 15:3-8" }],
        },
        {
          index: 2,
          title: "The Ascension",
          shortReflection: "Christ opens the path to heaven and sends us forth: the disciple lives on mission.",
          longReflection: "Jesus ascends but does not abandon us. He entrusts the mission to the Church, teaching us to look toward heaven while serving on earth.",
          intention: "Pray for a missionary spirit and the courage to witness to the faith in daily life.",
          scriptures: [{ ref: "Acts 1:6-11" }, { ref: "Mt 28:18-20" }],
        },
        {
          index: 3,
          title: "The Descent of the Holy Spirit",
          shortReflection: "The Spirit transforms fear into courage, sending the Church out to the world.",
          longReflection: "At Pentecost, the Holy Spirit fills the Church with gifts. Where the Spirit acts, unity and perseverance are born.",
          intention: "Pray for docility to the Holy Spirit and the gifts needed for your specific vocation.",
          scriptures: [{ ref: "Acts 2:1-13" }, { ref: "Gal 5:22-25" }],
        },
        {
          index: 4,
          title: "The Assumption of Mary",
          shortReflection: "Mary is a sign of hope: God fulfills His promises and lifts up the lowly.",
          longReflection: "In the Assumption, we contemplate the destiny God has prepared for His children. Mary anticipates the final victory of grace.",
          intention: "Pray for hope, purity, and the grace to live with your heart set on heaven.",
          scriptures: [{ ref: "Rev 12:1-6" }, { ref: "Lk 1:46-55" }],
        },
        {
          index: 5,
          title: "The Coronation of Mary",
          shortReflection: "Humility is exalted: Mary reigns by serving and interceding for us.",
          longReflection: "The Coronation reveals the logic of the Kingdom: those who make themselves small are raised up. Mary leads us directly to her Son.",
          intention: "Pray for trust in Mary's intercession and a faithful love for the Church.",
          scriptures: [{ ref: "Rev 12:1" }, { ref: "Jn 2:1-11" }],
        },
      ],
    },

    luminosos: {
      label: "Luminous Mysteries (Thursday)",
      themeHint: "light, revelation, discipleship, Eucharist, conversion",
      items: [
        {
          index: 1,
          title: "The Baptism of Jesus in the Jordan",
          shortReflection: "The Father reveals the Son: remembering your baptism is remembering who you are.",
          longReflection: "At the Jordan, Jesus identifies with us. The Father declares: 'This is my beloved Son.' We are called to live as children of God.",
          intention: "Pray for fidelity to your baptismal promises and daily conversion.",
          scriptures: [{ ref: "Mt 3:13-17" }, { ref: "Rom 6:3-4" }],
        },
        {
          index: 2,
          title: "The Wedding Feast at Cana",
          shortReflection: "Mary intercedes and Jesus transforms water into wine: God renews love.",
          longReflection: "Mary notices the need and tells us: 'Do whatever He tells you.' God changes the quality of our lives when we let Him act.",
          intention: "Pray for the renewal of faith in your home and practical obedience to Jesus' Word.",
          scriptures: [{ ref: "Jn 2:1-11" }, { ref: "Ps 34:9" }],
        },
        {
          index: 3,
          title: "The Proclamation of the Kingdom",
          shortReflection: "The Kingdom is at hand: the Gospel calls for a real change of heart.",
          longReflection: "Jesus announces the Kingdom and heals. He doesn't just inform; He transforms. This mystery calls us to abandon what distances us from God.",
          intention: "Pray for sincere repentance and the strength to change habits that lead away from God.",
          scriptures: [{ ref: "Mk 1:14-15" }, { ref: "Lk 15:1-7" }],
        },
        {
          index: 4,
          title: "The Transfiguration",
          shortReflection: "Light sustains faith: listening to Jesus is the way to endure the cross.",
          longReflection: "The glory of Christ appears to strengthen the disciples. Prayer transfigures us from within, giving meaning to our perseverance.",
          intention: "Pray for a luminous faith and the grace to be transformed by God's presence.",
          scriptures: [{ ref: "Lk 9:28-36" }, { ref: "2 Pet 1:16-18" }],
        },
        {
          index: 5,
          title: "The Institution of the Eucharist",
          shortReflection: "Jesus becomes our food: the Eucharist is the heart of the Christian life.",
          longReflection: "At the Last Supper, Christ gives His Body and Blood. Those who live by the Eucharist learn to love with constancy and reverence.",
          intention: "Pray for a deeper love for the Eucharist and a thirst for holiness.",
          scriptures: [{ ref: "Lk 22:14-20" }, { ref: "Jn 6:51-58" }],
        },
      ],
    },
  };

  function getDefaultMysterySetForWeekday(d) {
    const day = d.getDay();
    if (day === 1 || day === 6) return "gozosos";
    if (day === 2 || day === 5) return "dolorosos";
    if (day === 3 || day === 0) return "gloriosos";
    return "luminosos";
  }

  function getFinalSuggestionBySet(setKey) {
    switch (setKey) {
      case "gozosos":
        return "Search for: 'How to live Mary's Fiat in daily life' and 'virtues of the hidden life in Nazareth'.";
      case "dolorosos":
        return "Search for: 'Uniting suffering to the Cross of Christ' and 'practical Christian forgiveness'.";
      case "gloriosos":
        return "Search for: 'Resurrection and Christian hope' and 'living the gifts of the Holy Spirit'.";
      default:
        return "Search for: 'Luminous mysteries and Eucharistic life' and 'the meaning of metanoia in the Gospel'.";
    }
  }

  window.ROSARY_DATASET.en = {
    PRAYERS,
    MYSTERIES,
    OPENING_HAIL_MARY_MEDITATIONS,
    getDefaultMysterySetForWeekday,
    getFinalSuggestionBySet,
  };
})();